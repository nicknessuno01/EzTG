<?php
require_once 'EzTG.php';
$callback = function($update, $EzTG) {
  $EzTG->sendMessage(array('chat_id' => $update->message->from->id, 'text' => 'wewe'));
};
$EzTG = new EzTG(array('token' => 'token', 'callback' => $callback));
