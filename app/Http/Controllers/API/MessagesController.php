<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Models\Message;
use App\Models\UserContact;
use App\Events\NewMessage;
use App\Models\FileMessage;
use App\Models\TextMessage;
use App\Models\PositionMessage;
use App\Models\Conversation;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Type\Integer;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Validation\Rule;


class MessagesController extends Controller
{

    public function getConversations($user_id)
    {
        //$user = Auth::user();

        $user_id =intval($user_id);
        try {
            //Chequea que exista el usuario
            $user = User::find($user_id);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            $conversations = array();
            $active_users = array();
            $active_groups = array();
            $userContacsGroupIds = array();
            $userContactsUserIds = array();

            $x = 0;
            $i = 0;
            $g = 0;

            //var_dump("User: " . $user_id);

            $userContacts = UserContact::where('user_id', $user_id)->get();

            foreach ($userContacts as $userContact) {

                //var_dump("Contact TYPE: " .$userContact->contact_type);
                if ($userContact->contact_type == "App\\User") {
                    //var_dump("USER_CONTACT_IND: " . $userContact);
                    $userContactsUserIds[$i] = $userContact['contact_id'];
                    $i++;
                } else {
                    //var_dump("USER_CONTACT_GROUP: " . $userContact);
                    $userContacsGroupIds[$g] = $userContact['contact_id'];
                    $g++;
                }
            }

            $active_conversations = Conversation::where('type', 'INDIVIDUAL')
                                                ->where('user_id_1', $user_id)
                                                ->whereIn('user_id_2', $userContactsUserIds)
                                                ->orWhere(function ($query) use ($user_id, $userContactsUserIds) {
                                                    $query->where('type', 'INDIVIDUAL')
                                                    ->where('user_id_2', $user_id)
                                                    ->whereIn('user_id_1', $userContactsUserIds);
                                                })
                                                ->orWhere(function ($query) use ($userContacsGroupIds) {
                                                    $query->where('type', 'GROUP')
                                                    ->whereIN('group_id', $userContacsGroupIds);
                                                })
                                                ->with('user_1')
                                                ->with('user_2')
                                                ->with('group')
                                                ->orderBy('updated_at', 'desc')
                                                ->get();


            $i = 0;
            $g = 0;

            foreach ($active_conversations as $x => $conversation) {
                //$contact_dest= array();

                $conversation_members = array();

                //Identifico el usuario o grupo DESTINO de la conversacion
                if ($active_conversations[$x]->user_id_1 != null && $active_conversations[$x]->user_id_1 != $user_id) {


                    $conversation_members[0]['user_id'] = $active_conversations[$x]->user_id_1;
                    $conversation_members[0]['name'] = $active_conversations[$x]->user_1->name;
                    $conversation_name = NULL;

                    $active_users[$i] = $active_conversations[$x]->user_id_1;

                    $last_visualizations = UserContact::where('user_id', $user_id)
                                                    ->where('contact_type',"App\\User")
                                                    ->where('contact_id', $active_users[$i])
                                                    ->get("last_read_at");

                    foreach($last_visualizations as $last_visualization){
                        //var_dump("LAST visualizacion: " . $last_visualization['last_read_at']);

                        $ammount_messages_no_read = count($conversation->messages->where('sender_id', '<>', $user_id)
                                                                                ->where('created_at', '>', $last_visualization['last_read_at']));

                        //var_dump("Cant de mjes sin leer para la conversación " .$conversation->id . ": " . $ammount_messages_no_read);
                    }
                    $i++;


                } elseif ($active_conversations[$x]->user_id_1 != null && $active_conversations[$x]->user_id_1 == $user_id) {

                    $conversation_members[0]['user_id'] = $active_conversations[$x]->user_id_2;
                    $conversation_members[0]['name'] = $active_conversations[$x]->user_2->name;
                    $conversation_name = NULL;

                    $active_users[$i] = $active_conversations[$x]->user_id_2;

                    $last_visualizations = UserContact::where('user_id', $user_id)
                                                    ->where('contact_type',"App\\User")
                                                    ->where('contact_id', $active_users[$i])
                                                    ->get("last_read_at");

                    foreach($last_visualizations as $last_visualization){
                        //var_dump("LAST visualizacion: " . $last_visualization['last_read_at']);

                        $ammount_messages_no_read = count($conversation->messages->where('sender_id', '<>', $user_id)
                                                                                ->where('created_at', '>', $last_visualization['last_read_at']));

                        //var_dump("Cant de mjes sin leer para la conversación " .$conversation->id . ": " . $ammount_messages_no_read);
                    }
                    $i++;

                } elseif ($active_conversations[$x]->group_id != null) {

                    $conversation_name = $active_conversations[$x]->group->name;
                    $ammount_messages_no_read = 0;

                    //Se arma un array con los miembros del grupo
                    $group_members = UserContact::select(["user_id","last_read_at"])
                                                    ->where('contact_type',"App\\Models\\Group")
                                                    ->where('contact_id', $active_conversations[$x]->group_id)
                                                    ->get();

                    foreach($group_members as $a => $group_member){
                        $conversation_members[$a]['user_id'] = $group_member['user_id'];
                        $conversation_members[$a]['last_read_at'] = $group_member['last_read_at'];

                        if($group_member['user_id'] == $user_id){

                            //Se calcula los mensajes no leidos por el usuario logueado de esa conversación
                            if($group_member['last_read_at'] <> NULL){
                                $ammount_messages_no_read = count($conversation->messages->where('sender_id', '<>', $user_id)
                                ->where('created_at', '>', $group_member['last_read_at']));
                            }else{
                                $ammount_messages_no_read = count($conversation->messages->where('sender_id', '<>', $user_id));
                            }

                        }
                    }

                    $active_groups[$g] = $active_conversations[$x]->group_id;

                    $g++;
                }

                $conversations[$x]['conversation_id']= $active_conversations[$x]->id;
                $conversations[$x]['conversation_name']= $conversation_name;
                $conversations[$x]['conversation_members']= $conversation_members;
                $conversations[$x]['ammount_no_read']= $ammount_messages_no_read;
            }
            //Guardo el usuario logueado dentro de los usuarios con conversación activa
            if($i > 0){
                $active_users[$i+1] = $user_id;
            }else{
                $active_users[$i] = $user_id;
            }

            sort($active_users);

            //var_dump($active_users[$i]);

            if (count($active_users) > 1 || count($active_groups) > 0) { //Existe alguna conversacion activa
                $x++;
            }

            return response()->json([
                'user_origin' => $user_id,
                'conversations' => $conversations,
            ]);
        }

        catch (QueryException $e) {
            throw new \Error('Hay un Error SQL');
        }

        catch (\Throwable $e) {

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }
    public function getMessagesFromConversation($user_id, $conversation_id)
    {
        //Se envía el id de la conversación por $conversation_id porque puede enviarse id=0 y sino rebotaría porque no existe en la BD
        //$user = Auth::user();
        $user_id = intval($user_id);
        $conversation_id = intval($conversation_id);

        try {
            //var_dump("Conversacion ID: " . $conversation_id);

            //Chequea que exista el usuario
            $user = User::find($user_id);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

                //Chequea que exista la conversacion
                $conversation = Conversation::where('id',$conversation_id)
                                            ->first();

                if (!$conversation) {
                    throw new AccessDeniedHttpException(__('No existe la conversación.'));

                } else {
                    //Chequea que la conversacion pertenezca al usuario logueado, si es así actualiza la fecha última de visualización de esta conversación/contacto
                    if ($conversation->type == "INDIVIDUAL") {

                        if($user_id !== $conversation->user_id_1 && $user_id !== $conversation->user_id_2){
                            throw new AccessDeniedHttpException(__('El usuario NO es parte de la conversación individual.'));
                        }else{
                            //Averigua cual es el contact_id (user) para luego actualizar la tabla user_contacts con la ultima fecha de visualización de la conversación con ese contacto
                            if($conversation->user_id_1 == $user_id){
                                $contact_id = $conversation->user_id_2;
                            }else{
                                $contact_id = $conversation->user_id_1;
                            }

                            //Actualiza la fecha última de visualización de esta conversación/contacto
                            UserContact::where('contact_type', "App\\User")
                                    ->where('user_id', $user->id)
                                    ->where('contact_id', $contact_id)
                                    ->update(['last_read_at' => now()]);
                        }

                    }elseif ($conversation->type == "GROUP") {

                        $user_valid_groups = UserContact::where('user_id', $user_id)
                                                        ->where('contact_type', "App\\Models\\Group")
                                                        ->where('contact_id', $conversation->group_id)->first();
                        if(!$user_valid_groups) {
                            throw new AccessDeniedHttpException(__('El usuario NO tiene permisos para acceder a la conversación grupal.'));
                        }

                        //Actualiza la fecha última de visualización de esta conversación/contacto
                        UserContact::where('contact_type', "App\\Models\\Group")
                                    ->where('user_id', $user->id)
                                    ->where('contact_id', $conversation->group_id)
                                    ->update(['last_read_at' => now()]);
                    }
                }

                //Devuelve los mensajes de una Conversacion
                $messages = Message::select(['conversation_id','sender_id','sender_id','message_type','message_id','created_at'])
                                    ->where('conversation_id', $conversation->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

                //TODO - ANALIZAR si se insertará visualización en la tabla message_visualizations


            //TODO - Devolución de la info necesaria, eliminar los datos NO necesarios
            return response()->json([
                'user_origin' => $user_id,
                'messages' => $messages,
            ]);
        }

        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {
            DB::rollBack();

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }

    }
    public function createTextMessage(Request $request)
    {
        //$user = Auth::user();
        //Se asume que TODAS las conversaciones ya están cargadas en la tabla de conversations

        DB::beginTransaction();
        try {
            //Chequea los campos de entrada
            $campos = $request->validate([
                'user_id' => ['required','integer', 'exists:users,id'],
                'conversation_id' => ['required','integer', 'exists:conversations,id'],
                'message' => ['required','string', 'max:255'],
            ]);

            $user = User::find($campos['user_id']);
            //var_dump($user);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            //Se busca a qué grupos pertenece el usuario logueado
            $userGroups = UserContact::where('user_id',$campos['user_id'])
                                    ->where('contact_type', "App\\Models\\Group")
                                    ->get();

            foreach($userGroups as $b => $userGroup){
                $user_groups[$b] = $userGroup['contact_id'];
            }


            $conversation = Conversation::where(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_1', $user->id);
                                            })
                                            ->orWhere(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_2', $user->id);
                                            })

                                            ->orWhere(function($q) use ($user_groups, $campos){
                                                $q->where('id', $campos['conversation_id']);
                                                $q->whereIn('group_id', $user_groups);
                                            })
                                            ->first();

            if (!$conversation) {
                throw new AccessDeniedHttpException(__('El usuario no forma parte de la conversación a la que desea enviar el mensaje.'));
            }

             //Según tipo de contact_type, chequea que exista el usuario o grupo destino (contact_id) como contacto del usuario logueado

            //  if($campos['conversation_type'] == "INDIVIDUAL"){
            //     $contact_dest = UserContact::where('user_id',$campos['user_id'])
            //                                 ->where('contact_type', "App\\User")
            //                                 ->where('contact_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$contact_dest) {
            //         throw new AccessDeniedHttpException(__('El usuario a quien se quiere enviar el mensaje, NO es un contacto válido.'));
            //     }

            //     //Si está OK el contact_dest -> se busca la conversación y sino se la crea
            //     $conversation = Conversation::where('type', 'INDIVIDUAL')
            //                                 ->where(function($q) use ($user, $campos){
            //                                     $q->where('user_id_1', $user->id);
            //                                     $q->where('user_id_2', $campos['contact_id']);
            //                                 })
            //                                 ->orWhere(function($q) use ($user, $campos){
            //                                     $q->where('user_id_2', $user->id);
            //                                     $q->where('user_id_1', $campos['contact_id']);
            //                                 })
            //                                 ->first();

            //     if (!$conversation){ //La conversacion NO existe, se crea antes del mensaje
            //         $conversation = Conversation::create([
            //             'type' => 'INDIVIDUAL',
            //             'user_id_1' => $user->id,
            //             'user_id_2' => $campos['contact_id'],
            //             ]);

            //         if (!$conversation) {
            //            throw new \Error('No se pudo crear la conversación.');
            //         }
            //     }

            //  }else{
            //     $contact_dest = UserContact::where('user_id',$campos['user_id'])
            //                                 ->where('contact_type', "App\\Models\\Group")
            //                                 ->where('contact_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$contact_dest) {
            //         throw new AccessDeniedHttpException(__('El usuario NO pertenece grupo al que se quiere enviar el mensaje.'));
            //     }

            //     //Si está OK el contact_dest -> se busca la conversación y sino se la crea
            //     $conversation = Conversation::where('type', 'GROUP')
            //                                 ->where('group_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$conversation){ //La conversacion NO existe, se crea antes del mensaje
            //         $conversation = Conversation::create([
            //             'type' => 'GROUP',
            //             'group_id' => $campos['contact_id'],
            //             ]);

            //         if (!$conversation) {
            //            throw new \Error('No se pudo crear la conversación.');
            //         }
            //     }
            //  }

            //Crea el mje de texto
            $text_message = TextMessage::create([
                'text' => $campos['message']
            ]);

            if (!$text_message) {
                throw new \Error('No se pudo crear el mensaje de texto.');
            }

            // Crea el mensaje y lo asocia a la conversacion
            $message = Message::create([
                'sender_id' => $user->id,
                'conversation_id' => $conversation->id,
                'message_type' => get_class($text_message),
                'message_id' => $text_message->id,
            ]);

            if (!$message) {
                throw new \Error('No se pudo crear el mensaje.');
            }

            Conversation::where('id',$conversation->id)
                          ->update(['updated_at' => now()]);

            DB::commit();

            //Se lanza evento de Nuevo Mensaje
            //broadcast(new NewMessage($message));

            return response()->json([
                'status' => 200,
                'message' => 'Creación del mensaje de TEXTO realizada con éxito',
                'conversation_id' => $message->conversation_id,
                "sender_id" => $message->sender_id,
                'message_created' => $message->message->text,
            ]);

        }

        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {
            DB::rollBack();

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }
    public function createFileMessage(Request $request)
    {
        //$user = Auth::user();

        $files = array();


        $validator = Validator::make(
            $request->all(),
            [
                'user_id' => ['required','integer', 'exists:users,id'],
                'conversation_id' => ['required','integer', 'exists:conversations,id'],
                'file' => ['required', 'array'],
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $validatorFiles = Validator::make(
            $request->all(),
            [
                //'file.*' => ['file','required', 'mimes:doc,pdf,docx,txt,zip,jpeg,png,bmp,xls,xlsx,mov,qt,mp4,mp3,m4a' ,'max:10240'],
                'file.*' => ['file','required', 'mimes:doc,pdf,docx,txt,zip,jpeg,bmp,xls,xlsx,mov,qt,mp4,mp3,m4a' ,'max:10240'],
                'description' => ['sometimes', 'array'],
                'description.*' => ['nullable', 'string'],
            ],[
                'file.*.mimes' => __('Los archivos sólo pueden ser doc,pdf,docx,txt,zip,jpeg,png,bmp,xls,xlsx,mov,qt,mp4,mp3,m4a'),
                'file.*.max' => __('Cada archivo no puede ser mayor a 10MB'),
            ]
        );

        //$countSentFiles = count($request->file);
        //$countErrorFiles = count($validatorFiles->errors());
        //var_dump("Cant TOTAL de files enviados:" . $countSentFiles);
        //var_dump("Cant de files con ERROR:" . $countErrorFiles);

        $j = 0; //índice para array de archivos con error
        $h = 0; //índice para array de archivos OK
        $filesWhithError = array();
        $filesOK = array();

        if ($validatorFiles->fails()) {
            // Se crea un array con los files que tiene error con el mje para luego mostrarlos y los archivos que están OK se deben crear como mjes
            $errors = $validatorFiles->errors();

            //Leo el JSON con la info de los archivos con error
            $errorFiles = json_decode($errors, true);

            foreach ($request->file('file') as $i => $file) {
                if (isset($errorFiles["file." . $i])) {
                    //var_dump("ENTRO a ERROR y el I es: " . $i);
                    $filename = $request->file[$i]->getClientOriginalName();

                    $filesWhithError[$j]['index']= $i;
                    $filesWhithError[$j]['original_file']= $filename;
                    $filesWhithError[$j]['text_error']= $errorFiles["file." . $i][0];

                    //var_dump("file.". $i . " SÍ tiene error:" . $filename);
                    //var_dump($errorFiles["file." . $i][0]);

                    $j++;
                }else{
                    $filesOK[$h]['index']= $i;
                    $h++;
                }
            }
        }

        if($j > 0 && $h == 0){ //Hay archivos con error y ninguno OK
            return response()->json([
                'message' => 'No se pudo crear el mensaje.',
                'messages_with_error' => $filesWhithError
            ]);
        }

        DB::beginTransaction();

        try {

            $campos = $request;

            $user = User::find($campos['user_id']);
            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            //Se busca a qué grupos pertenece el usuario logueado
            $userGroups = UserContact::where('user_id',$campos['user_id'])
                                    ->where('contact_type', "App\\Models\\Group")
                                    ->get();

            foreach($userGroups as $b => $userGroup){
                $user_groups[$b] = $userGroup['contact_id'];
            }


            $conversation = Conversation::where(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_1', $user->id);
                                            })
                                            ->orWhere(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_2', $user->id);
                                            })

                                            ->orWhere(function($q) use ($user_groups, $campos){
                                                $q->where('id', $campos['conversation_id']);
                                                $q->whereIn('group_id', $user_groups);
                                            })
                                            ->first();

            if (!$conversation) {
                throw new AccessDeniedHttpException(__('El usuario no forma parte de la conversación a la que desea enviar el mensaje.'));
            }

             //Según tipo de contact_type, chequea que exista el usuario o grupo destino (contact_id) como contacto del usuario logueado

            $messages_created = array();

            foreach ($request->file('file') as $index => $file) {

                if (!isset($errorFiles["file." . $index])) {
                    $original_filename = $file->getClientOriginalName();

                    $name = explode('.', $original_filename);
                    $cant = count($name);

                    $filename  = "";

                    //Concateno si hubieran varios . sólo me interesa separar el último
                    for ($i=0;$i<=$cant-2;$i++) {
                        if ($i!=0) {
                            $filename  = $filename . '.';
                        }
                        $filename  = $filename . $name[$i];
                    }

                    $filename  = $filename .'_' . $index . '_' . time() . '.' . $name[$cant-1];

                    $file->storeAs('public/files', $filename);

                    $files[] = [
                        'file' => 'files/' . $filename,
                        'original_file' => $original_filename,
                        'description' => isset($campos['description'][$index]) ? $campos['description'][$index] : $file->getClientOriginalName(),
                    ];

                    //Crea el mje de file
                    $file_message = FileMessage::create([
                        'file' => 'files/' . $filename,
                        'original_file' => $original_filename,
                        'description' => isset($campos['description'][$index]) ? $campos['description'][$index] : $file->getClientOriginalName(),
                    ]);

                    if (!$file_message) {
                        throw new \Error('No se pudo crear el file_message.');
                    }

                    $messages_created[$index]['file'] = 'files/' . $filename;
                    $messages_created[$index]['original_file'] = $original_filename;
                    $messages_created[$index]['description'] = isset($campos['description'][$index]) ? $campos['description'][$index] : $file->getClientOriginalName();

                    // Crea el mensaje y lo asocia a la conversacion
                    $message = Message::create([
                        'sender_id' => $user->id,
                        'conversation_id' => $conversation->id,
                        'message_type' => get_class($file_message),
                        'message_id' => $file_message->id
                    ]);

                    if (!$message) {
                        throw new \Error('No se pudo crear el mensaje.');
                    }
                }
            }
            Conversation::where('id',$conversation->id)
                         ->update(['updated_at' => now()]);

            DB::commit();

            //Se lanza evento de Nuevo Mensaje
            //broadcast(new NewMessage($message));

            if($j > 0 && $h > 0){ //Hay archivos con error y otros OK
                return response()->json([
                    'status' => 200,
                    'message' => 'Creación de mensaje de tipo FILE realizada con éxito',
                    'conversation_id' => $message->conversation_id,
                    "sender_id" => $message->sender_id,
                    'message_created' => $messages_created,
                    'messages_with_error' => $filesWhithError
                ]);
            }else{
                return response()->json([
                    'status' => 200,
                    'message' => 'Creación de mensaje de tipo FILE realizada con éxito',
                    'conversation_id' => $message->conversation_id,
                    "sender_id" => $message->sender_id,
                    'message_created' => $messages_created,
                ]);
            }


        }

        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {

            DB::rollBack();
            if (count($files) > 0) {
                foreach ($files as $file) {
                $filename = $file['file'];
                Storage::disk('public')->delete($filename);
                }
            }

            if ($files) {
                $file_message->files()->delete();
            }

            if(isset($message)) {
                $message->delete();
                $file_message->delete();
            }

            $code = $e->getCode() ? $e->getCode() : 500;
            // return response()->json([
            //     'status' => $e->getCode() ? $e->getCode() : 500,
            //     'message' => $e->getMessage()

            // ], $code);
            return response()->json(['errors' => $e->getMessage()], 400);

        }
    }
    public function createPositionMessage(Request $request)
    {
        //$user = Auth::user();

        DB::beginTransaction();
        try {

            //Chequea los campos de entrada
            $campos = $request->validate([
                'user_id' => ['required','integer', 'exists:users,id'],
                'conversation_id' => ['required','integer', 'exists:conversations,id'],
                'lat' => ['required','numeric'],
                'lon' => ['required','numeric'],
                'alt' => ['required','numeric'],
            ]);

            $user = User::find($campos['user_id']);
            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

            //Se busca a qué grupos pertenece el usuario logueado
            $userGroups = UserContact::where('user_id',$campos['user_id'])
                                    ->where('contact_type', "App\\Models\\Group")
                                    ->get();

            foreach($userGroups as $b => $userGroup){
                $user_groups[$b] = $userGroup['contact_id'];
            }


            $conversation = Conversation::where(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_1', $user->id);
                                            })
                                            ->orWhere(function($q) use ($user, $campos){
                                                    $q->where('id', $campos['conversation_id']);
                                                    $q->where('user_id_2', $user->id);
                                            })

                                            ->orWhere(function($q) use ($user_groups, $campos){
                                                $q->where('id', $campos['conversation_id']);
                                                $q->whereIn('group_id', $user_groups);
                                            })
                                            ->first();

            if (!$conversation) {
                throw new AccessDeniedHttpException(__('El usuario no forma parte de la conversación a la que desea enviar el mensaje.'));
            }

            //Según tipo de contact_type, chequea que exista el usuario o grupo destino (contact_id) como contacto del usuario logueado

            // if($campos['contact_type'] == "INDIVIDUAL"){
            //     $contact_dest = UserContact::where('user_id',$campos['user_id'])
            //                                 ->where('contact_type', "App\\User")
            //                                 ->where('contact_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$contact_dest) {
            //         throw new AccessDeniedHttpException(__('El usuario a quien se quiere enviar el mensaje, NO es un contacto válido.'));
            //     }

            //     //Si está OK el contact_dest -> se busca la conversación y sino se la crea
            //     $conversation = Conversation::where('type', 'INDIVIDUAL')
            //                                 ->where(function($q) use ($user, $campos){
            //                                     $q->where('user_id_1', $user->id);
            //                                     $q->where('user_id_2', $campos['contact_id']);
            //                                 })
            //                                 ->orWhere(function($q) use ($user, $campos){
            //                                     $q->where('user_id_2', $user->id);
            //                                     $q->where('user_id_1', $campos['contact_id']);
            //                                 })
            //                                 ->first();

            //     if (!$conversation){ //La conversacion NO existe, se crea antes del mensaje
            //         $conversation = Conversation::create([
            //             'type' => 'INDIVIDUAL',
            //             'user_id_1' => $user->id,
            //             'user_id_2' => $campos['contact_id'],
            //             ]);

            //         if (!$conversation) {
            //            throw new \Error('No se pudo crear la conversación.');
            //         }
            //     }

            // }else{
            //     $contact_dest = UserContact::where('user_id',$campos['user_id'])
            //                                 ->where('contact_type', "App\\Models\\Group")
            //                                 ->where('contact_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$contact_dest) {
            //         throw new AccessDeniedHttpException(__('El usuario NO pertenece grupo al que se quiere enviar el mensaje.'));
            //     }

            //     //Si está OK el contact_dest -> se busca la conversación y sino se la crea
            //     $conversation = Conversation::where('type', 'GROUP')
            //                                 ->where('group_id', $campos['contact_id'])
            //                                 ->first();

            //     if (!$conversation){ //La conversacion NO existe, se crea antes del mensaje
            //         $conversation = Conversation::create([
            //             'type' => 'GROUP',
            //             'group_id' => $campos['contact_id'],
            //             ]);

            //         if (!$conversation) {
            //            throw new \Error('No se pudo crear la conversación.');
            //         }
            //     }
            // }

            //Crea el mje de posición
            $position_message = PositionMessage::create([
                'lat' => $campos['lat'],
                'lon' => $campos['lon'],
                'alt' => $campos['alt']
              ]);

              if (!$position_message) {
                throw new \Error('No se pudo crear el mensaje de posición.');
              }

            // Crea el mensaje y lo asocia a la conversacion
            $message = Message::create([
                'sender_id' => $user->id,
                'conversation_id' => $conversation->id,
                'message_type' => get_class($position_message),
                'message_id' => $position_message->id,
            ]);

            if (!$message) {
                throw new \Error('No se pudo crear el mensaje.');
            }

            Conversation::where('id',$conversation->id)
                          ->update(['updated_at' => now()]);

            DB::commit();

            //Se lanza evento de Nuevo Mensaje
            //broadcast(new NewMessage($message));

            return response()->json([
                'status' => 200,
                'message' => 'Creación del mensaje de POSICIÓN realizada con éxito',
                'conversation_id' => $message->conversation_id,
                "sender_id" => $message->sender_id,
                'message_created' => $message->message
            ]);

        }

        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {
            DB::rollBack();

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }
    }


        //Prueba de api con validación de token via Middleware
        public function getMessagesFromConversationWithMiddlewareTOKEN($conversation_id)
        {
            //INICIA Validación del TOKEN enviado en el header
            $jwt = "";

            foreach (getallheaders() as $name => $value) {
                //echo "$name: $value\n";
                if($name == "Authorization"){
                    $jwt = substr($value, 7); //Se extrae 'Bearer ' y nos quedamos con el token
                    //echo $jwt . "\n";
                    break;
                }
            }
    
            // split the jwt
            $tokenParts = explode('.', $jwt);
            $payload = base64_decode($tokenParts[1]);
    
            //echo $payload . "\n";
    
            $user_id = json_decode($payload)->user_id;
            //echo "USER_ID: " . $user_id . "\n";
    
            //FINALIZA extracción de userId desde el TOKEN
    
            //Se envía el id de la conversación por $conversation_id porque puede enviarse id=0 y sino rebotaría porque no existe en la BD
            //$user = Auth::user();

            $user_id = intval($user_id);
            $conversation_id = intval($conversation_id);
    
            try {
                //var_dump("Conversacion ID: " . $conversation_id);
    
                //Chequea que exista el usuario
                $user = User::find($user_id);
    
                if ($user == null) {
                    throw new AccessDeniedHttpException(__('No existe el usuario.'));
                }
    
                    //Chequea que exista la conversacion
                    $conversation = Conversation::where('id',$conversation_id)
                                                ->first();
    
                    if (!$conversation) {
                        throw new AccessDeniedHttpException(__('No existe la conversación.'));
    
                    } else {
                        //Chequea que la conversacion pertenezca al usuario logueado, si es así actualiza la fecha última de visualización de esta conversación/contacto
                        if ($conversation->type == "INDIVIDUAL") {
    
                            if($user_id !== $conversation->user_id_1 && $user_id !== $conversation->user_id_2){
                                throw new AccessDeniedHttpException(__('El usuario NO es parte de la conversación individual.'));
                            }else{
                                //Averigua cual es el contact_id (user) para luego actualizar la tabla user_contacts con la ultima fecha de visualización de la conversación con ese contacto
                                if($conversation->user_id_1 == $user_id){
                                    $contact_id = $conversation->user_id_2;
                                }else{
                                    $contact_id = $conversation->user_id_1;
                                }
    
                                //Actualiza la fecha última de visualización de esta conversación/contacto
                                UserContact::where('contact_type', "App\\User")
                                        ->where('user_id', $user->id)
                                        ->where('contact_id', $contact_id)
                                        ->update(['last_read_at' => now()]);
                            }
    
                        }elseif ($conversation->type == "GROUP") {
    
                            $user_valid_groups = UserContact::where('user_id', $user_id)
                                                            ->where('contact_type', "App\\Models\\Group")
                                                            ->where('contact_id', $conversation->group_id)->first();
                            if(!$user_valid_groups) {
                                throw new AccessDeniedHttpException(__('El usuario NO tiene permisos para acceder a la conversación grupal.'));
                            }
    
                            //Actualiza la fecha última de visualización de esta conversación/contacto
                            UserContact::where('contact_type', "App\\Models\\Group")
                                        ->where('user_id', $user->id)
                                        ->where('contact_id', $conversation->group_id)
                                        ->update(['last_read_at' => now()]);
                        }
                    }
    
                    //Devuelve los mensajes de una Conversacion
                    $messages = Message::select(['conversation_id','sender_id','sender_id','message_type','message_id','created_at'])
                                        ->where('conversation_id', $conversation->id)
                                        ->orderBy('created_at', 'desc')
                                        ->paginate(10);
    
                    //TODO - ANALIZAR si se insertará visualización en la tabla message_visualizations
    
    
                //TODO - Devolución de la info necesaria, eliminar los datos NO necesarios
                return response()->json([
                    'user_origin' => $user_id,
                    'messages' => $messages,
                ]);
            }
    
            catch (QueryException $e) {
                throw new \Error('Error SQL');
            }
    
            catch (\Throwable $e) {
                DB::rollBack();
    
                $code = $e->getCode() ? $e->getCode() : 500;
                return response()->json([
                    'status' => $e->getCode() ? $e->getCode() : 500,
                    'message' => $e->getMessage()
                ], $code);
            }
    
        }

    //Prueba de api con validación de token
    public function getMessagesFromConversationTOKEN($conversation_id)
    {
        //INICIA Validación del TOKEN enviado en el header
        $jwt = "";
        $client = "";
        foreach (getallheaders() as $name => $value) {
            //echo "$name: $value\n";
            if($name == "Authorization"){
                $jwt = substr($value, 7); //Se extrae 'Bearer ' y nos quedamos con el token
                echo $jwt . "\n";
            }else if($name == "client"){
                $client = $value; 
                echo "CLIENTE: " . $client . "\n";
            }
        }

        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        //echo $header . "\n";
        //echo $payload . "\n";
        //echo $signature_provided . "\n";

        //Se consulta qué CLIENT es el que está queriendo acceder a la API, para extraer la SECRET del .env
        //$client = json_decode($payload)->client;
        //echo "CLIENTE: " . $client . "\n";

        $secret = getenv($client);
        echo "SECRET: " . $secret . "\n";

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        $expiration = json_decode($payload)->exp;

        if($expiration - time() < 0){
            $is_token_expired = 1;
            echo $is_token_expired . " El token EXPIRÓ\n";
        }else {
            $is_token_expired = 0;
            echo $is_token_expired . " El token NO EXPIRÓ\n";;
        }

        // build a signature based on the header and payload using the secret
        $base64_url_header = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64_url_payload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $signature = hash_hmac('SHA256', $base64_url_header . "." . $base64_url_payload, $secret, true);
        $base64_url_signature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        // verify it matches the signature provided in the jwt
        if($base64_url_signature === $signature_provided){
            $is_signature_valid = 1;
            echo $is_signature_valid . " La FIRMA es Válida\n";
        }else{
            $is_signature_valid = 0;
            echo $is_signature_valid . " La FIRMA NO es Válida\n";
        }

        if ($is_token_expired || !$is_signature_valid) {
            echo "El TOKEN NO es válido". "\n";
        } else {
            echo "El TOKEN es válido". "\n";
            $user_id = json_decode($payload)->user_id;
            echo "USER_ID: " . $user_id . "\n";
        }


        //FINALIZA validación de TOKEN

        //Se envía el id de la conversación por $conversation_id porque puede enviarse id=0 y sino rebotaría porque no existe en la BD
        //$user = Auth::user();
        //$user_id = intval($user_id);
        $conversation_id = intval($conversation_id);

        try {
            //var_dump("Conversacion ID: " . $conversation_id);

            //Chequea que exista el usuario
            $user = User::find($user_id);

            if ($user == null) {
                throw new AccessDeniedHttpException(__('No existe el usuario.'));
            }

                //Chequea que exista la conversacion
                $conversation = Conversation::where('id',$conversation_id)
                                            ->first();

                if (!$conversation) {
                    throw new AccessDeniedHttpException(__('No existe la conversación.'));

                } else {
                    //Chequea que la conversacion pertenezca al usuario logueado, si es así actualiza la fecha última de visualización de esta conversación/contacto
                    if ($conversation->type == "INDIVIDUAL") {

                        if($user_id !== $conversation->user_id_1 && $user_id !== $conversation->user_id_2){
                            throw new AccessDeniedHttpException(__('El usuario NO es parte de la conversación individual.'));
                        }else{
                            //Averigua cual es el contact_id (user) para luego actualizar la tabla user_contacts con la ultima fecha de visualización de la conversación con ese contacto
                            if($conversation->user_id_1 == $user_id){
                                $contact_id = $conversation->user_id_2;
                            }else{
                                $contact_id = $conversation->user_id_1;
                            }

                            //Actualiza la fecha última de visualización de esta conversación/contacto
                            UserContact::where('contact_type', "App\\User")
                                    ->where('user_id', $user->id)
                                    ->where('contact_id', $contact_id)
                                    ->update(['last_read_at' => now()]);
                        }

                    }elseif ($conversation->type == "GROUP") {

                        $user_valid_groups = UserContact::where('user_id', $user_id)
                                                        ->where('contact_type', "App\\Models\\Group")
                                                        ->where('contact_id', $conversation->group_id)->first();
                        if(!$user_valid_groups) {
                            throw new AccessDeniedHttpException(__('El usuario NO tiene permisos para acceder a la conversación grupal.'));
                        }

                        //Actualiza la fecha última de visualización de esta conversación/contacto
                        UserContact::where('contact_type', "App\\Models\\Group")
                                    ->where('user_id', $user->id)
                                    ->where('contact_id', $conversation->group_id)
                                    ->update(['last_read_at' => now()]);
                    }
                }

                //Devuelve los mensajes de una Conversacion
                $messages = Message::select(['conversation_id','sender_id','sender_id','message_type','message_id','created_at'])
                                    ->where('conversation_id', $conversation->id)
                                    ->orderBy('created_at', 'desc')
                                    ->paginate(10);

                //TODO - ANALIZAR si se insertará visualización en la tabla message_visualizations


            //TODO - Devolución de la info necesaria, eliminar los datos NO necesarios
            return response()->json([
                'user_origin' => $user_id,
                'messages' => $messages,
            ]);
        }

        catch (QueryException $e) {
            throw new \Error('Error SQL');
        }

        catch (\Throwable $e) {
            DB::rollBack();

            $code = $e->getCode() ? $e->getCode() : 500;
            return response()->json([
                'status' => $e->getCode() ? $e->getCode() : 500,
                'message' => $e->getMessage()
            ], $code);
        }

    }
}
