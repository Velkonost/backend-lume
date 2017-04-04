<?php

switch ($Module) {
    case 'showBoards':
        showBoards($CONNECT, $_POST['id']);
        break;
    case 'getBoardInfo':
        getBoardInfo($CONNECT, $_POST['bid']);
        break;
    case 'getBoardParticipants':
        getBoardParticipants($CONNECT, $_POST['bid']);
        break;
    case 'getColumnInfo':
        getColumnInfo($CONNECT, $_POST['cid'], $_POST['id']);
        break;
    case 'getCardInfo':
        getCardInfo($CONNECT, $_POST['card_id']);
        break;
    case 'getCardParticipants':
        getCardParticipants($CONNECT, $_POST['card_id']);
        break;
    case 'cardAddComment':
        cardAddComment($CONNECT, $_POST['card_id'], $_POST['id'], $_POST['text']);
        break;
    case 'leaveCard' :
        leaveCard($CONNECT, $_POST['id'], $_POST['card_id']);
        break;
    case 'leaveBoard':
        leaveBoard($CONNECT, $_POST['id'], $_POST['bid']);
        break;
    case 'inviteInBoard':
        inviteInBoard($CONNECT, $_POST['id'], $_POST['bid']);
        break;
    case 'inviteInCard':
        inviteInCard($CONNECT, $_POST['id'], $_POST['card_id']);
        break;
    case 'changeBoardSettings':
        changeBoardSettings($CONNECT, $_POST['bid'], $_POST['bname'], $_POST['bdescription']);
        break;
    case 'changeCardSettings':
        changeCardSettings($CONNECT, $_POST['card_id'], $_POST['card_name'], $_POST['card_description']);
        break;
    case 'getInBoardToInvite':
        getInBoardToInvite($CONNECT, $_POST['bid']);
        break;
    case 'getBoardColumns':
        getBoardColumns($CONNECT, $_POST['bid']);
        break;
    case 'moveCard':
        moveCard($CONNECT, $_POST['card_id'], $_POST['cid']);
        break;
    case 'addColumn':
        addColumn($CONNECT, $_POST['name'], $_POST['bid']);
        break;
    case 'addCard':
        addCard($CONNECT, $_POST['name'], $_POST['description'], $_POST['cid']);
        break;
    case 'addBoard':
        addBoard($CONNECT, $_POST['id'], $_POST['name'], $_POST['description']);
        break;
    case 'changeColumnSettings':
        changeColumnSettings($CONNECT, $_POST['bid'], $_POST['name'], $_POST['previous_name'], $_POST['position']);
        break;
    case 'changeCardColor':
        changeCardColor($CONNECT, $_POST['card_id'], $_POST['card_color']);
        break;

}

/**
 * Показывает список досок пользователя
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 */
function showBoards($connect, $post_id) {
    $post_id = FormChars($post_id);

    $response = array();
    $bids = array();

    $contributedBoards = mysqli_query($connect, "SELECT * FROM `in_board` WHERE `uid` = '$post_id'");

    while ($board = mysqli_fetch_assoc($contributedBoards)) {
        $bid = $board['bid'];
        $getName = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `name` FROM `boards` WHERE `id` = $bid"));

        array_push($bids, $bid);
        $response[$bid] = $getName['name'];
    }
    $response['bids'] = $bids;

    echo json_encode($response);
}

/**
 * Получение информации о доске
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 */
function getBoardInfo($connect, $post_bid) {
    $post_bid = FormChars($post_bid);

    $response = array();

    $uids = array();
    $cids = array();
    $cNames = array();


    $boardInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `name`, `description` FROM `boards` WHERE `id` = '$post_bid'"));
    $boardName = $boardInfo['name'];
    $boardDescription = $boardInfo['description'];

    $columns = mysqli_query($connect, "SELECT * FROM `columns` WHERE `board_id` = '$post_bid'");

    while ($column = mysqli_fetch_assoc($columns)) {
        $columnId = $column['id'];
        $columnName = $column['name'];
        $columnPosition = $column['position'];

        $cids[$columnPosition] = $columnId;
        $cNames[$columnId] = $columnName;

    }

    ksort($cids);

    $sortedColumns = array_values(array_unique($cids));
    foreach ($sortedColumns as $key => $value) {

        $column = array();
        $column['id'] = $value;
        $column['name'] = $cNames[$value];

        $response[$value] = $column;
    }


    $boardParticipants = mysqli_query($connect, "SELECT `uid` FROM `in_board` WHERE `bid` = '$post_bid'");

    while ($user = mysqli_fetch_assoc($boardParticipants)) {
        $userId = $user['uid'];

        $userInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login`, `avatar` FROM `users` WHERE `id` = '$userId'"));

        $participant = array();
        $participant['id'] = $userId;
        $participant['login'] = $userInfo['login'];
        $participant['avatar'] = $userInfo['avatar'];

        $userId .= 'user';
        array_push($uids, $userId);

        $response[$userId] = $participant;

    }

    $response['cids'] = array_values(array_unique($sortedColumns));
    $response['uids'] = array_values(array_unique($uids));
    $response['bname'] = $boardName;
    $response['bdescription'] = $boardDescription;

    echo json_encode($response);
}

/**
 * Получение участников доски
 *
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 */
function getBoardParticipants($connect, $post_bid) {
    $post_bid = FormChars($post_bid);

    $response = array();
    $ids = array();

    $boardParticipants = mysqli_query($connect, "SELECT `uid` FROM `in_board` WHERE `bid` = '$post_bid'");

    while ($user = mysqli_fetch_assoc($boardParticipants)) {
        $userId = $user['uid'];

        $userInfo = mysqli_fetch_assoc(mysqli_query($connect,
            "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

        $participant = array();
        $participant['id'] = $userId;
        $participant['login'] = $userInfo['login'];
        $participant['avatar'] = $userInfo['avatar'];
        $participant['name'] = $userInfo['name'];
        $participant['surname'] = $userInfo['surname'];

        array_push($ids, $userId);

        $response[$userId] = $participant;

    }

    $response['ids'] = array_values(array_unique($ids));
    echo json_encode($response);
}

/**
 * Получение информации о колонке и принадлежащих ей карточках
 *
 * @param $connect - соединение
 * @param $post_cid - идентификатор колонки
 * @param $post_id - идентификатор пользователя
 */
function getColumnInfo($connect, $post_cid, $post_id) {

    $post_cid = FormChars($post_cid);
    $post_id = FormChars($post_id);

    $response = array();

    $uids = array();
    $cardIds = array();
    $cardNames = array();
    $cardColors = array();

    $cards = mysqli_query($connect,
        "SELECT `id`, `name`, `description`, `position`, `color` FROM `cards` WHERE `column_id` = '$post_cid'");

    while ($card = mysqli_fetch_assoc($cards)) {
        $cardId = $card['id'];
        $cardName = $card['name'];
        $cardPosition = $card['position'];
        $cardColor = $card['color'];

        $cardIds[$cardPosition] = $cardId;
        $cardNames[$cardId] = $cardName;
        $cardColors[$cardId] = $cardColor;

    }

    ksort($cardIds);

    $sortedCards = array_values(array_unique($cardIds));
    foreach ($sortedCards as $key => $value) {

        $card = array();
        $card['name'] = $cardNames[$value];
        $card['card_color'] = $cardColors[$value];

        $isBelong = false;

        $cardParticipants = mysqli_query($connect, "SELECT `uid` FROM `in_card` WHERE `cid` = '$value'");

        $participants = array();
        $count = 0;
        while ($user = mysqli_fetch_assoc($cardParticipants)) {

            $participant = array();

            $userId = $user['uid'];
            $count++;

            if ($userId == $post_id) $isBelong = true;

            $userInfo = mysqli_fetch_assoc(mysqli_query($connect,
                "SELECT `login`, `avatar` FROM `users` WHERE `id` = $userId"));

            $participant['login'] = $userInfo['login'];
            $participant['avatar'] = $userInfo['avatar'];

            $participants[$userId] = $participant;
            array_push($uids, $userId);
        }

        $card['participants'] = $participants;
        $card['amount'] = $count;
        $card['belong'] = $isBelong;

        $response[$value] = $card;
    }

    $response['cids'] = array_values(array_unique($sortedCards));
    $response['uids'] = array_values(array_unique($uids));

    echo json_encode($response);

}

/**
 * Получение информации о карточке
 *
 * @param $connect - соединение
 * @param $post_card_id - идентификатор карточки
 */
function getCardInfo($connect, $post_card_id) {

    $post_card_id = FormChars($post_card_id);

    $response = array();
    $ids = array();

    $comments = array();
    $texts = array();
    $users = array();
    $times = array();

    $cardInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `description`, `color` FROM `cards` WHERE `id` = '$post_card_id'"));
    $cardParticipants = mysqli_query($connect, "SELECT `uid` FROM `in_card` WHERE `cid` = '$post_card_id'");

    while ($user = mysqli_fetch_assoc($cardParticipants)) {
        $userId = $user['uid'];

        $userInfo = mysqli_fetch_assoc(mysqli_query($connect,
            "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

        $participant = array();
        $participant['id'] = $userId;
        $participant['login'] = $userInfo['login'];
        $participant['avatar'] = $userInfo['avatar'];
        $participant['name'] = $userInfo['name'];
        $participant['surname'] = $userInfo['surname'];

        array_push($ids, $userId);

        $response[$userId] = $participant;

    }

    $getComments = mysqli_query($connect,
        "SELECT `send_id`, `text`, `date`, `id` FROM `comments` WHERE `card_id` = '$post_card_id'");

    while ($comment = mysqli_fetch_assoc($getComments)) {
        $userId = $comment['send_id'];

        $getUserLogin = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login` FROM `users` WHERE `id` = '$userId'"));

        $userLogin = $getUserLogin['login'];

        $commentId = $comment['id'];
        $commentTime = $comment['date'];
        $commentText = $comment['text'];

        $comments[$commentTime] = $commentId;
        $texts[$commentId] = $commentText;
        $users[$commentId] = $userLogin;
        $times[$commentId] = $commentTime;
    }

    ksort($comments);
    $sortedComments = array_values(array_unique($comments));

    foreach ($sortedComments as $key => $value) {

        $comment = array();
        $comment['id'] = $value;
        $comment['text'] = $texts[$value];
        $comment['user'] = $users[$value];
        $comment['date'] = $times[$value];

        $response[$value . "comment"] = $comment;
    }

    $response['comment_ids'] = array_values(array_unique($sortedComments));

    $response['card_description'] = $cardInfo['description'];
    $response['card_color'] = $cardInfo['color'];

    $response['uids'] = array_values(array_unique($ids));
    echo json_encode($response);

}

/**
 * Получение участников карточки
 *
 * @param $connect - соединение
 * @param $post_card_id - идентификатор карточки
 */
function getCardParticipants($connect, $post_card_id) {

    $post_card_id = FormChars($post_card_id);

    $response = array();
    $ids = array();

    $cardParticipants = mysqli_query($connect, "SELECT `uid` FROM `in_card` WHERE `cid` = '$post_card_id'");

    while ($user = mysqli_fetch_assoc($cardParticipants)) {
        $userId = $user['uid'];

        $userInfo = mysqli_fetch_assoc(mysqli_query($connect,
            "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

        $participant = array();
        $participant['id'] = $userId;
        $participant['login'] = $userInfo['login'];
        $participant['avatar'] = $userInfo['avatar'];
        $participant['name'] = $userInfo['name'];
        $participant['surname'] = $userInfo['surname'];

        array_push($ids, $userId);

        $response[$userId] = $participant;

    }

    $response['ids'] = array_values(array_unique($ids));
    echo json_encode($response);

}

/**
 * Добавление комментария в карточку
 *
 * @param $connect - соединение
 * @param $post_card_id - идентификатор карточки
 * @param $post_id - идентификатор пользователя
 * @param $post_text - текст комментария
 */
function cardAddComment($connect, $post_card_id, $post_id, $post_text) {

    $post_card_id = FormChars($post_card_id);
    $post_id = FormChars($post_id);
    $post_text = FormChars($post_text);

    mysqli_query($connect, "INSERT INTO `comments` VALUES ('', '$post_id', '$post_text', '$post_card_id', NOW())");

}

/**
 * Выход из карточки
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_card_id - идентификатор карточки
 */
function leaveCard($connect, $post_id, $post_card_id) {
    $post_id = FormChars($post_id);
    $post_card_id = FormChars($post_card_id);

    mysqli_query($connect, "DELETE FROM `in_card` WHERE `uid` = '$post_id' AND `cid` = '$post_card_id'");
}

/**
 * Выход из доски
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_bid - идентификатор доски
 */
function leaveBoard($connect, $post_id, $post_bid) {

    $post_id = FormChars($post_id);
    $post_bid = FormChars($post_bid);

    mysqli_query($connect, "DELETE FROM `in_board`  WHERE `uid` = '$post_id' AND `bid` = '$post_bid'");

}

/**
 * Добавление пользователя в доску
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_bid - идентификатор доски
 */
function inviteInBoard($connect, $post_id, $post_bid) {
    $post_id = FormChars($post_id);
    $post_bid = FormChars($post_bid);

    $checkUser = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `in_board` WHERE `uid` = '$post_id' AND `bid` = '$post_bid'"));

    if (!$checkUser) mysqli_query($connect, "INSERT INTO `in_board` VALUES ('', '$post_id', '$post_bid')");
}

/**
 * Добавление пользователя в карточку
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_card_id - идентификатор карточки
 */
function inviteInCard($connect, $post_id, $post_card_id) {

    $post_id = FormChars($post_id);
    $post_card_id = FormChars($post_card_id);

    $checkUser = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `in_card` WHERE `uid` = '$post_id' AND `cid` = '$post_card_id'"));

    if (!$checkUser) mysqli_query($connect, "INSERT INTO `in_card` VALUES ('', '$post_id', '$post_card_id')");

}

/**
 * Изменение настроек доски
 *
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 * @param $post_bname - название доски
 * @param $post_bdescription - описание доски
 */
function changeBoardSettings($connect, $post_bid, $post_bname, $post_bdescription) {
    $post_bid = FormChars($post_bid);
    $post_bname = FormChars($post_bname);
    $post_bdescription = FormChars($post_bdescription);

    mysqli_query($connect,
        "UPDATE `boards` SET `name` = '$post_bname', `description` = '$post_bdescription' WHERE `id` = $post_bid");
}

/**
 * Изменение настроек карточки
 *
 * @param $connect - соединение
 * @param $post_card_id - идентификатор карточки
 * @param $post_card_name - название карточки
 * @param $post_card_description - описание карточки
 */
function changeCardSettings($connect, $post_card_id, $post_card_name, $post_card_description) {

    $post_card_id = FormChars($post_card_id);
    $post_card_name = FormChars($post_card_name);
    $post_card_description = FormChars($post_card_description);

    mysqli_query($connect,
        "UPDATE `cards` SET `name` = '$post_card_name', `description` = '$post_card_description' WHERE `id` = $post_card_id");

}

/**
 * Получение списка участников доски
 *
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 */
function getInBoardToInvite($connect, $post_bid) {
    $post_bid = FormChars($post_bid);

    $response = array();
    $ids = array();

    $getContacts = mysqli_query($connect, "SELECT `uid` FROM `in_board` WHERE `bid` = '$post_bid'");

    while ($getContact = mysqli_fetch_assoc($getContacts)) {
        $userId = $getContact['uid'];

        $getUserInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id`, `login`, `name`, `surname`, `avatar` 
				FROM `users` WHERE `id` = '$userId'"));

        $userInfo = array();

        foreach ($getUserInfo as $key => $value) {
            $userInfo[$key] = $value;
        }
        $response[$userInfo['id']] = $userInfo;
        array_push($ids, $userInfo['id']);
    }
    $response['ids'] = array_values(array_unique($ids));;

    echo json_encode($response);
}

/**
 * Получение колонок доски
 *
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 */
function getBoardColumns($connect, $post_bid) {

    $post_bid = FormChars($post_bid);

    $response = array();
    $cids = array();
    $cNames = array();

    $columns = mysqli_query($connect, "SELECT * FROM `columns` WHERE `board_id` = '$post_bid'");

    while ($column = mysqli_fetch_assoc($columns)) {
        $columnId = $column['id'];
        $columnName = $column['name'];
        $columnPosition = $column['position'];

        $cids[$columnPosition] = $columnId;
        $cNames[$columnId] = $columnName;

    }

    ksort($cids);

    $sortedColumns = array_values(array_unique($cids));
    foreach ($sortedColumns as $key => $value) {

        $column = array();
        $column['id'] = $value;
        $column['name'] = $cNames[$value];

        $response[$value] = $column;
    }

    $response['cids'] = array_values(array_unique($sortedColumns));

    echo json_encode($response);

}

/**
 * Перемещение карточки
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор карточки
 * @param $post_cid - идентификатор колонки, в которую перемещают
 */
function moveCard($connect, $post_id, $post_cid) {

    $post_id = FormChars($post_id);
    $post_cid = FormChars($post_cid);

    $getPosition = mysqli_query($connect, "SELECT `position` FROM `cards` WHERE `column_id` = '$post_cid'");
    $curPosition = 0;
    while ($position = mysqli_fetch_assoc($getPosition)) {
        $prevPosition = $position['position'];
        if ($prevPosition > $curPosition) $curPosition = $prevPosition;
    }
    $curPosition++;

    $getPosition = mysqli_query($connect, "SELECT `position` FROM `cards` WHERE `column_id` = '$post_cid'");
    $curPosition = 0;
    while ($position = mysqli_fetch_assoc($getPosition)) {
        $prevPosition = $position['position'];
        if ($prevPosition > $curPosition) $curPosition = $prevPosition;
    }
    $curPosition++;

    $getInfoOfThisCard = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `position`, `column_id` FROM `cards` WHERE `id` = '$post_id'"));

    // change position of another cards
    mysqli_query($connect, "UPDATE `cards` SET `position` = `position` - 1 WHERE `position` > '$getInfoOfThisCard[position]' AND `column_id` = '$getInfoOfThisCard[column_id]' ");

    mysqli_query($connect, "UPDATE `cards` SET `column_id` = '$_POST[cid]', `position` = $curPosition WHERE `id` = $post_id");

}

/**
 * Добавление колонки
 *
 * @param $connect - соединение
 * @param $post_name - название колонки
 * @param $post_bid - описание колонки
 */
function addColumn($connect, $post_name, $post_bid) {
    $post_name = FormChars($post_name);
    $post_bid = FormChars($post_bid);

    $getPosition = mysqli_query($connect, "SELECT `position` FROM `columns` WHERE `board_id` = '$post_bid'");

    $curPosition = 0;
    while ($position = mysqli_fetch_assoc($getPosition)) {
        $prevPosition = $position['position'];

        if ($prevPosition > $curPosition) $curPosition = $prevPosition;
    }
    $curPosition++;

    mysqli_query($connect, "INSERT INTO `columns`  VALUES ('', '$post_name', '$post_bid', $curPosition)");
}

/**
 * Добавление карточки
 *
 * @param $connect - соединение
 * @param $post_name - название карточки
 * @param $post_description - описание карточки
 * @param $post_cid - идентификатор колонки
 */
function addCard($connect, $post_name, $post_description, $post_cid) {
    $post_name = FormChars($post_name);
    $post_description = FormChars($post_description);
    $post_cid = FormChars($post_cid);

    $getPosition = mysqli_query($connect, "SELECT `position` FROM `cards` WHERE `column_id` = '$post_cid'");

    $curPosition = 0;
    while ($position = mysqli_fetch_assoc($getPosition)) {
        $prevPosition = $position['position'];
        if ($prevPosition > $curPosition) $curPosition = $prevPosition;
    }
    $curPosition++;

    mysqli_query($connect, "INSERT INTO `cards` VALUES ('', '$post_name', '$post_cid', '$post_description', $curPosition, '000000')");
}

/**
 * Добавление доски
 *
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_name - название доски
 * @param $post_description - описание доски
 */
function addBoard($connect, $post_id, $post_name, $post_description) {
    $post_id = FormChars($post_id);
    $post_name = FormChars($post_name);
    $post_description = FormChars($post_description);

    $time = $_SERVER['REQUEST_TIME'];

    mysqli_query($connect, "INSERT INTO `boards`  VALUES ('', '$post_name', '$post_description', $time)");

    $row = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `boards` WHERE `date` = $time"));
    mysqli_query($connect, "INSERT INTO `in_board`  VALUES ('', '$post_id', '$row[id]')");
}

/**
 * Изменение настроек колонки
 *
 * @param $connect - соединение
 * @param $post_bid - идентификатор доски
 * @param $post_name - название колонки
 * @param $post_previous_name - предыдущее название колонки
 * @param $post_position - позиция колонки в доске
 */
function changeColumnSettings($connect, $post_bid, $post_name, $post_previous_name, $post_position) {
    $post_bid = FormChars($post_bid);
    $post_name = FormChars($post_name);
    $post_previous_name = FormChars($post_previous_name);
    $post_position = FormChars($post_position);

    mysqli_query($connect, "UPDATE `columns` SET `name` = '$post_name' WHERE `board_id` = '$post_bid' AND `position` = '$post_position'");
}

/**
 * Изменение цвета карточки
 *
 * @param $connect - соединение
 * @param $post_card_id - идентификатор карточки
 * @param $post_card_color - новый цвет карточки
 */
function changeCardColor($connect, $post_card_id, $post_card_color) {
    $post_card_id = FormChars($post_card_id);

    mysqli_query($connect, "UPDATE `cards` SET `color` = '$post_card_color' WHERE `id` = '$post_card_id'");
}



?>