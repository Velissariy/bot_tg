<?php

namespace App\Actions;

use App\Queue\Queueable;

class EventSender implements Queueable {
  private string $receiver;
  private string $message;
  protected $telegramApi;
  protected $queue;

  public function sendMessage(string $receiver, string $message) {
    $this->toQueue($receiver, $message);
  }

  public function __construct($telegramApi, $queue) {
    $this->telegramApi = $telegramApi;
    $this->queue = $queue;
  }

  public function toQueue(...$args): void {
    $this->receiver = $args[0];
    $this->message = $args[1];
    $this->queue->sendMessage([
      'receiver' => $this->receiver,
      'message' => $this->message,
    ]);
  }

  public function handle(): void {
    $this->telegramApi->sendMessage($this->receiver, date('d.m.y H:i') . " " . $this->message);
  }
}