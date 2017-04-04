<?php

switch ($Module) {
    case 'showDialogs':
        showDialogs($CONNECT, $_POST['user_id']);
        break;
    case 'showMessages':
        showMessages($CONNECT, $_POST['did'], $_POST['addresseeId']);
        break;
    case 'sendMessage':
        sendMessage($CONNECT, $_POST['did'], $_POST['id'], $_POST['text']);
        break;
    case 'createDialog':
        createDialog($CONNECT, $_POST['senderId'], $_POST['addresseeId']);
        break;
}

/**
 * Список диалогов пользователя
 *
 * @param $connect - соединение
 * @param $post_user_id - идентификатор пользователя
 */
function showDialogs($connect, $post_user_id)
{

    $post_user_id = FormChars($post_user_id);

    $response = array();
    $ids = array();
    $idDialogs = array();
    $dialogStatus = array();

    $idsEmpty = array();
    $idDialogsEmpty = array();

    $getReceiveDialogs = mysqli_query($connect, "SELECT `status`, `receive`, `date`, `id` FROM `dialog` WHERE `send` = '$post_user_id'");
    $getSendDialogs = mysqli_query($connect, "SELECT `status`, `send`, `date`, `id` FROM `dialog` WHERE `receive` = '$post_user_id'");

    while ($receiveDialogs = mysqli_fetch_assoc($getReceiveDialogs)) {
        $userId = $receiveDialogs['receive'];
        $lastMessage = $receiveDialogs['date'];
        $dialogId = $receiveDialogs['id'];
        $status = $receiveDialogs['status'];

        if ($status == 1) {
            $ids[$lastMessage] = $userId;
            $idDialogs[$userId] = $dialogId;
        } else {
            $idsEmpty[$lastMessage] = $userId;
            $idDialogsEmpty[$userId] = $dialogId;
        }
        $dialogStatus[$userId] = $status;
    }


    while ($sendDialogs = mysqli_fetch_assoc($getSendDialogs)) {
        $userId = $sendDialogs['send'];
        $lastMessage = $sendDialogs['date'];
        $dialogId = $sendDialogs['id'];
        $status = $sendDialogs['status'];

        if ($status == 1) {
            $ids[$lastMessage] = $userId;
            $idDialogs[$userId] = $dialogId;
        } else {
            $idsEmpty[$lastMessage] = $userId;
            $idDialogsEmpty[$userId] = $dialogId;
        }
        $dialogStatus[$userId] = $status;
    }

    ksort($ids);

    $sortedIds = array_reverse(array_values(array_unique($ids)));

    foreach ($sortedIds as $key => $value) {
        $userId = $value;

        $getUserInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id`, `login`, `name`, `surname`, `avatar` 
				FROM `users` WHERE `id` = '$userId'"));

        $userInfo = array();

        foreach ($getUserInfo as $key => $value) {
            $userInfo[$key] = $value;
        }
        $userInfo['did'] = $idDialogs[$userInfo['id']];

        $countUnread = mysqli_fetch_row(mysqli_query($connect, "SELECT COUNT(`status`) FROM `message` WHERE `did` = $userInfo[did] AND `user` != $post_user_id AND `status` = 1"));

        $userInfo['unread'] = $countUnread[0];

        $userInfo['status'] = $dialogStatus[$userInfo['id']];

        $response[$userInfo['id']] = $userInfo;
    }

    $response['ids'] = array_values(array_unique($sortedIds));

    echo json_encode($response);

}

/**
 * Список сообщений диалога
 *
 * @param $connect - соединение
 * @param $post_did - идентификатор диалога
 * @param $post_addressee_id - идентификатор адресата
 */
function showMessages($connect, $post_did, $post_addressee_id)
{
    $post_did = FormChars($post_did);
    $post_addressee_id = FormChars($post_addressee_id);

    $response = array();
    $messagesIds = array();

    $messages = array();
    $texts = array();
    $users = array();
    $statuses = array();
    $times = array();

    $getMessages = mysqli_query($connect, "SELECT `user`, `text`, `status`, `date`, `id` FROM `message` WHERE `did` = '$post_did'");

    while ($message = mysqli_fetch_assoc($getMessages)) {
        $userId = $message['user'];
        $messageId = $message['id'];
        $messageTime = $message['date'];
        $messageText = $message['text'];
        $messageStatus = $message['status'];

        $messages[$messageTime] = $messageId;
        $texts[$messageId] = $messageText;
        $users[$messageId] = $userId;
        $statuses[$messageId] = $messageStatus;
        $times[$messageId] = $messageTime;
    }

    ksort($messages);
    $sortedMessages = array_values(array_unique($messages));
    foreach ($sortedMessages as $key => $value) {

        $message = array();
        $message['id'] = $value;
        $message['text'] = $texts[$value];
        $message['user'] = $users[$value];
        $message['status'] = $statuses[$value];
        $message['date'] = $times[$value];

        $response[$value] = $message;
    }

    $response['mids'] = array_values(array_unique($sortedMessages));

    echo json_encode($response);

    mysqli_query($connect, "UPDATE `message` SET `status` = '0' WHERE `did` = $post_did AND `user` = $post_addressee_id");

}

/**
 * Отправка сообщения
 *
 * @param $connect - соединение
 * @param $post_did - идентификатор диалога
 * @param $post_id - идентификатор ползователя
 * @param $post_text - текст сообщения
 */
function sendMessage($connect, $post_did, $post_id, $post_text)
{

    $post_did = FormChars($post_did);
    $post_id = FormChars($post_id);
    $post_text = FormChars($post_text);

    mysqli_query($connect, "INSERT INTO `message` VALUES ('', '$post_did', '$post_id', 
				'$post_text', NOW(), '1')");

    $checkDialog = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `send`, `receive` FROM `dialog` WHERE `id` = '$post_did'"));

    $receive = $checkDialog['send'] == $post_id ? $checkDialog['receive'] : $checkDialog['send'];

    mysqli_query($connect, "UPDATE `dialog` SET `status` = 1, `send` = $post_id, `receive` = $receive, `date` = NOW() WHERE `id` = $post_did");

}

/**
 * Создание нового диалога
 *
 * @param $connect - соединение
 * @param $post_sender_id - идентификатор адресанта
 * @param $post_addressee_id - идентификатор адресата
 */
function createDialog($connect, $post_sender_id, $post_addressee_id)
{
    $post_sender_id = FormChars($post_sender_id);
    $post_addressee_id = FormChars($post_addressee_id);

    $response = array();

    mysqli_query($connect, "INSERT INTO `dialog` VALUES ('', '0', '$post_sender_id', '$post_addressee_id', NOW())");
    $getDialogId = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `dialog` WHERE `send` = '$post_sender_id' AND `receive` = '$post_addressee_id'"));

    $response['did'] = $getDialogId['id'];

    echo json_encode($response);
}

