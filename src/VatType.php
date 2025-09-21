<?php
/**
 * This file is part of the it-quasar/atol-online library.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ItQuasar\AtolOnline;

/**
 * Номер налога в ККТ.
 */
class VatType
{
    /** @var string Без НДС */
    public const NONE = 'none';

    /** @var string НДС чека по ставке 0%. */
    public const VAT0 = 'vat0';

    /** @var string НДС чека по ставке 5%. */
    public const VAT5 = 'vat5';

    /** @var string НДС чека по ставке 7%. */
    public const VAT7 = 'vat7';

    /** @var string НДС чека по ставке 10%. */
    public const VAT10 = 'vat10';

    /** @var string НДС чека по ставке 18%. */
    public const VAT18 = 'vat18';

    /** @var string НДС чека по ставке 20%. */
    public const VAT20 = 'vat20';

    /** @var string НДС чека по расчетной ставке 5/105. */
    public const VAT105 = 'vat105';

    /** @var string НДС чека по расчетной ставке 7/107. */
    public const VAT107 = 'vat107';

    /** @var string НДС чека по расчетной ставке 10/110. */
    public const VAT110 = 'vat110';

    /** @var string НДС чека по расчетной ставке 20/120. */
    public const VAT120 = 'vat120';

    /** @var string НДС чека по расчетной ставке 18/118. */
    public const VAT118 = 'vat118';
}
