<?php
/**
 * This file is part of the it-quasar/atol-online library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ItQuasar\AtolOnline;

use InvalidArgumentException;
use ItQuasar\AtolOnline\Exception\SdkException;
use function is_null;

/**
 * Позиция чека.
 */
class ItemV5 implements RequestPart
{
    /** @var string */
    private $name = null;

    /** @var float */
    private $price = null;

    /** @var float */
    private $quantity = null;

    /** @var null|int */
    private $measure = null;

    /** @var float */
    private $sum = null;

    /** @var null|string */
    private $paymentMethod = null;

    /** @var null|int/ */
    private $paymentObject = null;

    /** @var null|Vat */
    private $vat = null;

    /** @var null|string */
    private $userData = null;

    /** @var null|AgentInfo */
    private $agentInfo = null;

    /** @var null|SupplierInfo */
    private $supplierInfo = null;

    /**
     * Создает позицию чека.
     *
     * @param null|string $name Название товара
     */
    public function __construct(?string $name = null)
    {
        $this->setName($name);
    }

    /**
     * Возвращает наименование товара.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Устанавливает наименование товара. Максимальная длина строки – 64 символа.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        if (mb_strlen($name) > 128) {
            throw new InvalidArgumentException('Name too big. Max length size = 128');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Возвращает цену в рублях.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Устанавлиает цену в рублях:
     * - целая часть не более 8 знаков;
     * - дробная часть не более 2 знаков.
     *
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): self
    {
        if ($price > 42_949_672.95) {
            throw new InvalidArgumentException('Price too big. Max = 42_949_672.95');
        }

        $this->price = $price;

        return $this;
    }

    /**
     * Возвращает количество/вес
     *
     * @return float
     */
    public function getQuantity(): float
    {
        return $this->quantity;
    }

    /**
     * Устанавливает количество/вес:
     * - целая часть не более 5 знаков;
     * - дробная часть не более 6 знаков.
     *
     * @param float $quantity
     *
     * @return $this
     */
    public function setQuantity(float $quantity): self
    {
        if ($quantity > 99999.999999) {
            throw new InvalidArgumentException('Quantity too big. Max = 99999.999999');
        }

        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Возвращает единицу измерения товара, работы, услуги, платежа, выплаты, иного предмета расчета.
     *
     * @return int
     */
    public function getMeasure(): int
    {
        return $this->measure;
    }

    /**
     * Устанавлиает единицу измерения товара, работы, услуги, платежа, выплаты, иного предмета расчета.
     *
     * Возможные значения:
     * - @param int $unit
     *
     * @return $this
     *
     * @see Measure::PIECE – штука/единица
     * @see Measure::GRAM – грамм
     * @see Measure::KILOGRAM – килограмм
     * @see Measure::TON – тонна
     * @see Measure::CENTIMETER – сантиметр
     * @see Measure::DECIMETER – дециметр
     * @see Measure::METER – метр
     * @see Measure::SQUARE_CENTIMETER – квадратный сантиметр
     * @see Measure::SQUARE_DECIMETER – квадратный дециметр
     * @see Measure::SQUARE_METER – квадратный метр
     * @see Measure::MILLILITER – миллилитр
     * @see Measure::LITER – литр
     * @see Measure::CUBIC_METER – кубический метр
     * @see Measure::KILOWATT_HOUR – киловатт-час
     * @see Measure::GIGACALORIE – гигакалория
     * @see Measure::DAY – день
     * @see Measure::HOUR – час
     * @see Measure::MINUTE – минута
     * @see Measure::SECOND – секунда
     * @see Measure::KILOBYTE – килобайт
     * @see Measure::MEGABYTE – мегабайт
     * @see Measure::GIGABYTE – гигабайт
     * @see Measure::TERABYTE – терабайт
     * @see Measure::OTHER – применяется при использовании иных единиц измерения
     */
    public function setMeasure(int $unit): self
    {
        if ($unit < 0 || $unit > 255) {
            throw new InvalidArgumentException('Invalid measurement unit. Min = 0, max = 255');
        }

        $this->measure = $unit;

        return $this;
    }

    /**
     * Возвращает сумму позиции в рублях.
     *
     * @return float
     */
    public function getSum(): float
    {
        return $this->sum;
    }

    /**
     * Устанавливает сумму позиции в рублях:
     * - целая часть не более 8 знаков;
     * - дробная часть не более 2 знаков.
     *
     * Если значение sum меньше/больше значения (price*quantity), то разница является скидкой/надбавкой на позицию
     * соответственно. В этих случаях происходит перерасчёт поля price для равномерного распределения
     * скидки/надбавки по позициям.
     *
     * @param float $sum
     *
     * @return $this
     */
    public function setSum(float $sum): self
    {
        if ($sum > 42_949_672.95) {
            throw new InvalidArgumentException('Sum too big. Max = 42_949_672.95');
        }

        $this->sum = $sum;

        return $this;
    }

    /**
     * Возвращает признак способа расчёта.
     *
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * Устанавливает признак способа расчёта.
     *
     * Возможные значения:
     * - @param string $method
     *
     * @return $this
     * @see PaymentMethod::ADVANCE – аванс.
     * - @see PaymentMethod::FULL_PAYMENT – полный расчет. Полная оплата, в том числе с учетом аванса
     *   (предварительной оплаты) в момент передачи
     * - @see PaymentMethod::PARTIAL_PAYMENT – частичный расчет и кредит. Частичная оплата предмета расчета в момент его
     *   передачи с последующей оплатой в кредит.
     * - @see PaymentMethod::CREDIT – передача в кредит. Передача предмета расчета без его оплаты в момент его передачи с
     *   последующей оплатой в кредит.
     * - @see PaymentMethod::CREDIT_PAYMENT – оплата кредита. Оплата предмета расчета после его передачи с оплатой в
     *   кредит (оплата кредита) предмета расчета
     *
     * @see PaymentMethod::FULL_PREPAYMENT – предоплата 100%. Полная предварительная оплата до момента передачи предмета расчета.
     * @see PaymentMethod::PREPAYMENT – предоплата. Частичная предварительная оплата до момента передачи предмета расчета.
     */
    public function setPaymentMethod(string $method): self
    {
        $this->paymentMethod = $method;

        return $this;
    }

    /**
     * Возвращает признак предмета расчёта.
     *
     * @return int
     */
    public function getPaymentObject(): int
    {
        return $this->paymentObject;
    }

    /**
     * Устанавливает признак предмета расчёта.
     *
     * Возможные значения:
     * - @param int $object
     *
     * @return $this
     * @see PaymentObjectV5::COMMODITY – товар. О реализуемом товаре, за исключением подакцизного товара
     *   (наименование и иные сведения, описывающие товар).
     * - @see PaymentObjectV5::EXCISE – подакцизный товар. О реализуемом подакцизном товаре (наименование и иные сведения,
     *   описывающие товар).
     * - @see PaymentObjectV5::JOB – работа. О выполняемой работе (наименование и иные сведения, описывающие работу).
     * - @see PaymentObjectV5::SERVICE – услуга. Об оказываемой услуге (наименование и иные сведения, описывающие услугу).
     * - @see PaymentObjectV5::GAMBLING_BET – ставка азартной игры. О приеме ставок при осуществлении деятельности по
     *   проведению азартных игр.
     * - @see PaymentObjectV5::GAMBLING_PRIZE – выигрыш азартной игры. О выплате денежных средств в виде выигрыша при
     *   осуществлении деятельности по проведению азартных игр.
     * - @see PaymentObjectV5::LOTTERY – лотерейный билет. О приеме денежных средств при реализации лотерейных билетов,
     *   электронных лотерейных билетов, приеме лотерейных ставок при осуществлении деятельности по проведению лотерей.
     * - @see PaymentObjectV5::LOTTERY_PRIZE – выигрыш лотереи. О выплате денежных средств в виде выигрыша при осуществлении
     *   деятельности по проведению лотерей.
     * - @see PaymentObjectV5::INTELLECTUAL_ACTIVITY – предоставление результатов интеллектуальной деятельности. О предоставлении
     *   прав на использование результатов интеллектуальной деятельности или средств индивидуализации.
     * - @see PaymentObjectV5::PAYMENT – платеж. Об авансе, задатке, предоплате, кредите, взносе в счет оплаты, пени, штрафе,
     *   вознаграждении, бонусе и ином аналогичном предмете расчета.
     * - @see PaymentObjectV5::AGENT_COMMISSION – агентское вознаграждение. О вознаграждении пользователя, являющегося платежным
     *   агентом (субагентом), банковским платежным агентом (субагентом), комиссионером, поверенным или иным агентом.
     * - @see PaymentObjectV5::ANOTHER – иной предмет расчета. О предмете расчета, не относящемуся к выше перечисленным предметам расчета.
     * - @see PaymentObjectV5::PROPERTY_RIGHT – имущественное право. О передаче имущественных прав.
     * - @see PaymentObjectV5::NON_OPERATING_GAIN – внереализационный доход. О внереализационном доходе.
     * - @see PaymentObjectV5::INSURANCE_PREMIUM – страховые взносы. О суммах расходов, уменьшающих сумму налога (авансовых
     *   платежей) в соответствии с пунктом 3.1 статьи 346.21 Налогового кодекса Российской Федерации.
     * - @see PaymentObjectV5::SALES_TAX – торговый сбор. О суммах уплаченного торгового сбора.
     * - @see PaymentObjectV5::RESORT_FEE – курортный сбор. О курортном сборе.
     *
     */
    public function setPaymentObject(int $object): self
    {
        $this->paymentObject = $object;

        return $this;
    }

    /**
     * Возвращает атрибуты налога на позицию
     *
     * @return Vat
     */
    public function getVat(): Vat
    {
        return $this->vat;
    }

    /**
     * Устанавлиает атрибуты налога на позицию.
     *
     * Необходимо передать либо сумму налога на позицию, либо сумму налога на чек. Если будут переданы и
     * сумма налога на позицию и сумма налога на чек, сервис учтет только сумму налога на чек.
     *
     * @param Vat $vat
     *
     * @return $this
     */
    public function setVat(Vat $vat): self
    {
        $this->vat = $vat;

        return $this;
    }

    /**
     * Возвращает дополнительный реквизит предмета расчета.
     *
     * @return string|null
     */
    public function getUserData(): ?string
    {
        return $this->userData;
    }

    /**
     * Устанавливает дополнительный реквизит предмета расчета.
     *
     * @param string|null $userData
     *
     * @return Item
     */
    public function setUserData(?string $userData): self
    {
        if (mb_strlen($userData) > 64) {
            throw new InvalidArgumentException('User data too big. Max length size = 64');
        }

        $this->userData = $userData;

        return $this;
    }

    /**
     * Возвращает атрибуты агента.
     *
     * @return AgentInfo|null
     */
    public function getAgentInfo(): ?AgentInfo
    {
        return $this->agentInfo;
    }

    /**
     * Устанавливает атрибуты агента.
     *
     * @param AgentInfo|null $agentInfo
     *
     * @return $this
     */
    public function setAgentInfo(?AgentInfo $agentInfo): self
    {
        $this->agentInfo = $agentInfo;

        return $this;
    }

    /**
     * Возвращает атрибуты поставщика.
     *
     * @return SupplierInfo|null
     */
    public function getSupplierInfo(): ?SupplierInfo
    {
        return $this->supplierInfo;
    }

    /**
     * Устанавливает атрибуты поставщика.
     *
     * @param SupplierInfo|null $supplierInfo
     *
     * @return $this
     */
    public function setSupplierInfo(?SupplierInfo $supplierInfo): self
    {
        $this->supplierInfo = $supplierInfo;

        return $this;
    }

    public function toArray(): array
    {
        if (is_null($this->name)) {
            throw new SdkException('Name required');
        }

        if (is_null($this->price)) {
            throw new SdkException('Price required');
        }

        if (is_null($this->sum)) {
            throw new SdkException('Sum required');
        }

        if (is_null($this->quantity)) {
            throw new SdkException('Quantity required');
        }

        if (is_null($this->measure)) {
            throw new SdkException('Measure required');
        }

        if (is_null($this->vat)) {
            throw new SdkException('Vat required');
        }

        if (is_null($this->paymentMethod)) {
            throw new SdkException('Payment method required');
        }

        if (is_null($this->paymentObject)) {
            throw new SdkException('Payment object required');
        }

        if (!is_null($this->agentInfo) && is_null($this->supplierInfo)) {
            throw new SdkException('Supplier info required if agent info sets.');
        }

        if (!is_null($this->supplierInfo) && is_null($this->agentInfo)) {
            throw new SdkException('Agent info required if supplier info sets.');
        }

        $result = [
            'name' => $this->name,
            'price' => round($this->price, 2),
            'quantity' => round($this->quantity, 3),
            'measure' => $this->measure,
            'sum' => round($this->sum, 2),
            'vat' => $this->vat->toArray(),
            'payment_method' => $this->paymentMethod,
            'payment_object' => $this->paymentObject,
        ];

        if (!is_null($this->agentInfo)) {
            $result['agent_info'] = $this->agentInfo->toArray();
        }

        if (!is_null($this->supplierInfo)) {
            $result['supplier_info'] = $this->supplierInfo->toArray();
        }

        if (!is_null($this->userData)) {
            $result['user_data'] = $this->userData;
        }

        return $result;
    }
}
