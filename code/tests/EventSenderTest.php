<?php

namespace Tests\Unit;

use App\Actions\EventSender;
use App\Queue\RabbitMQ;
use App\TelegramApi\TelegramApi;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Actions\EventSender
 */
class EventSenderTest extends TestCase {
  private $telegramApiMock;
  private $queueMock;
  private EventSender $eventSender;

  protected function setUp(): void {
    $this->telegramApiMock = Mockery::mock(TelegramApi::class);
    $this->queueMock = Mockery::mock(RabbitMQ::class);
    $this->eventSender = new EventSender($this->telegramApiMock, $this->queueMock);
  }

  protected function tearDown(): void {
    Mockery::close();
  }

  public function testToQueueSendsMessageToQueue() {
    $this->queueMock->shouldReceive('sendMessage')
      ->once()
      ->with([
        'receiver' => 'test_receiver',
        'message' => 'test_message',
      ]);

    $this->eventSender->toQueue('test_receiver', 'test_message');
    $this->assertTrue(true);
  }

  public function testHandleSendsMessage() {
    $this->telegramApiMock->shouldReceive('sendMessage')
      ->with('test_receiver', Mockery::on(function ($arg) {
        return is_string($arg) && strpos($arg, 'test_message') !== false;
      }))
      ->once();

    $this->queueMock->shouldReceive('sendMessage')
      ->once()
      ->with([
        'receiver' => 'test_receiver',
        'message' => 'test_message',
      ]);

    $this->eventSender->toQueue('test_receiver', 'test_message');
    $this->eventSender->handle();

    $this->assertTrue(true);
  }
}
