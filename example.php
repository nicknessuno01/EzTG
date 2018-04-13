<?php
require_once 'EzTG.php';
$callback = function($update, $EzTG) {
  if (isset($update->message->text) and $update->message->text == '/start') {
    $EzTG->sendMessage(array('chat_id' => $update->message->from->id, 'text' => 'wewe'));
  }
  
};
$EzTG = new EzTG(array('token' => 'token', 'callback' => $callback));
