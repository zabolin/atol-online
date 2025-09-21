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
 * Коррекция.
 */
class CorrectionInfo implements RequestPart
{
    /** @const string Самостоятельно */
    const TYPE_SELF = 'self';

    /** @const string По предписанию */
    const TYPE_INSTRUCTION = 'instruction';

    /** @var array $typeList */
    private $typeList = [
        self::TYPE_SELF,
        self::TYPE_INSTRUCTION
    ];

    /** @var string|null $type */
    private $type = null;

    /** @var null|DateTime $baseDate */
    private $baseDate = null;

    /** @var null|string */
    private $baseNumber = null;

    /**
     * Возвращает тип коррекции.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Устанавливает тип коррекии.
     *
     * Возможные значения:
     * @param string $type
     *
     * @return $this
     * @see CorrectionInfo::TYPE_SELF – самостоятельно;
     * @see CorrectionInfo::TYPE_INSTRUCTION - по предписанию.
     *
     */
    public function setType(string $type): self
    {
        if (!in_array($type, $this->typeList)) {
            throw new InvalidArgumentException('Type must be one of: ' . implode(', ', $this->typeList));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Возвращает дату документа основания для коррекции
     *
     * @return DateTime
     */
    public function getBaseDate(): DateTime
    {
        return $this->baseDate;
    }

    /**
     * Устанавлиает дату документа основания для коррекции
     *
     * @param DateTime $baseDate
     *
     * @return $this
     */
    public function setBaseDate(DateTime $baseDate): self
    {
        $this->baseDate = $baseDate;

        return $this;
    }

    /**
     * Возвращает номер документа основания для коррекции.
     *
     * @return string
     */
    public function getBaseNumber(): string
    {
        return $this->baseNumber;
    }

    /**
     * Устанавливет номер документа основания для коррекции.
     *
     * @param string $baseNumber
     *
     * @return $this
     */
    public function setBaseNumber(string $baseNumber): self
    {
        if (mb_strlen($baseNumber) > 32) {
            throw new InvalidArgumentException('BaseNumber too big. Max length size = 32');
        }

        $this->baseNumber = $baseNumber;

        return $this;
    }

    public function toArray(): array
    {
        if (is_null($this->type)) {
            throw new SdkException('Type required');
        }

        if (is_null($this->baseDate)) {
            throw new SdkException('Base date required');
        }

        if ($this->type == self::TYPE_INSTRUCTION && is_null($this->baseNumber)) {
            throw new SdkException('Base number required');
        }

        $result = [
            'type' => $this->type,
            'base_date' => $this->baseDate->format('d.m.Y'),
        ];

        if ($this->type == self::TYPE_INSTRUCTION) {
            $result['base_number'] = $this->baseNumber;
        }

        return $result;
    }
}
