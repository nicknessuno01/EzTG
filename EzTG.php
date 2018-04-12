<?php
class EzTGException extends Exception { }
class EzTG {
  private $settings;
  private $offset;
  public function __construct($settings) {
    if (!isset($settings['endpoint'])) $settings['endpoint'] = 'https://api.telegram.org';
    if (!isset($settings['token'])) $this->error('Invalid token.');
    if (!isset($settings['callback'])) $this->error('Invalid callback.');
    if (!is_callable($settings['callback'])) $this->error('Invalid callback.');
    $this->settings = $settings;
    if (php_sapi_name() === 'cli') {
      $this->offset = -1;
      $this->getUpdates();
    } else {
      $this->processUpdate(json_decode(file_get_contents('php://input')));
    }
  }
  private function getUpdates() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'].'/bot'.$this->settings['token'].'/'.'getUpdates');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    while (true) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'offset='.$this->offset);
      $result = json_decode(curl_exec($ch));
      if ($result->ok == 0) $this->error($result->description);
      foreach ($result->result as $update) {
        if (isset($update->update_id)) $this->offset = $update->update_id+1;
        $this->processUpdate($update);
      }
    }
    curl_close($ch);
  }
  private function processUpdate($update) {
    $this->settings['callback']($update, $this);
  }
  protected function error($e) {
    throw new EzTGException($e);
  }
  public function __call($name, $arguments) {
    if (!isset($arguments[0])) $arguments[0] = array();
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->settings['endpoint'].'/bot'.$this->settings['token'].'/'.urlencode($name));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments[0]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = json_decode(curl_exec($ch));
    curl_close($ch);
    if ($result->ok == 0) $this->error($result->description);
  }
}
