<?php
/**
 * This file is part of the it-quasar/atol-online library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ItQuasar\AtolOnline;

use DateTime;
use InvalidArgumentException;
use ItQuasar\AtolOnline\Exception\SdkException;

/**
 * Чек «Коррекция возврата прихода».
 */
class SellRefundCorrectionV5 implements Request
{
  /** @var string $externalId */
  private $externalId = null;

  /** @var CorrectionV5 $correction */
  private $correction = null;

  /** @var Service $service */
  private $service = null;

  /** @var DateTime $timestamp */
  private $timestamp = null;

  /**
   * Возвращает идентификатор документа внешней системы.
   *
   * @return string
   */
  public function getExternalId(): string
  {
    return $this->externalId;
  }

  /**
   * Устанавливает идентификатор документа внешней системы.
   *
   * Уникальный среди всех документов, отправляемых в данную группу ККТ.
   *
   * Предназначен для защиты от потери документов при разрывах связи – всегда можно подать повторно чек с таким же
   * external_ID. Если данный external_id известен системе будет возвращен UUID, ранее присвоенный этому чеку,
   * иначе чек добавится в систему с присвоением нового UUID.
   *
   * Максимальная длина строки – 256 символов
   *
   * @param string $externalId
   *
   * @return $this
   */
  public function setExternalId(string $externalId): self
  {
    if (mb_strlen($externalId) > 256) {
      throw new InvalidArgumentException('ExternalId too big. Max length size = 256');
    }

    $this->externalId = $externalId;

    return $this;
  }

  /**
   * Возвращает коррекцию.
   *
   * @return CorrectionV5
   */
  public function getCorrection(): CorrectionV5
  {
    return $this->correction;
  }

  /**
   * Устанавливает коррекцию.
   *
   * @param CorrectionV5 $correction
   *
   * @return $this
   */
  public function setCorrection(CorrectionV5 $correction): self
  {
    $this->correction = $correction;

    return $this;
  }

  /**
   * Возвращете служебный раздел.
   *
   * @return Service
   */
  public function getService(): Service
  {
    return $this->service;
  }

  /**
   * Устанавливает служебный раздел.
   *
   * @param Service $service
   *
   * @return $this
   */
  public function setService(Service $service): self
  {
    $this->service = $service;

    return $this;
  }

  /**
   * Возвращает дату и время документа внешней системы.
   *
   * @return DateTime
   */
  public function getTimestamp(): DateTime
  {
    return $this->timestamp;
  }

  /**
   * Устанавливает дату и время документа внешней системы.
   *
   * @param DateTime $timestamp
   *
   * @return $this
   */
  public function setTimestamp(DateTime $timestamp): self
  {
    $this->timestamp = $timestamp;

    return $this;
  }

  public function toArray(): array
  {
    if (is_null($this->externalId)) {
      throw new SdkException('ExternalId required');
    }

    if (is_null($this->correction)) {
      throw new SdkException('Correction required');
    }

    if (is_null($this->service)) {
      throw new SdkException('Service required');
    }

    if (is_null($this->timestamp)) {
      throw new SdkException('Timestamp required');
    }

    return [
      'external_id' => $this->externalId,
      'correction' => $this->correction->toArray(),
      'service' => $this->service->toArray(),
      'timestamp' => $this->timestamp->format('d.m.Y H:i:s'),
    ];
  }

  public function getOperation(): string
  {
    return 'sell_refund_correction';
  }
}
