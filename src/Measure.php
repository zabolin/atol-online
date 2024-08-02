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
 * Признак предмета расчета
 */
class Measure
{
    /** @var int Применяется для предметов расчета, которые могут быть реализованы поштучно или единицами. */
    const PIECE = 0;

    /** @var int Грамм */
    const GRAM = 10;

    /** @var int Килограмм */
    const KILOGRAM = 11;

    /** @var int Тонна */
    const TON = 12;

    /** @var int Сантиметр */
    const CENTIMETER = 20;

    /** @var int Дециметр */
    const DECIMETER = 21;

    /** @var int Метр */
    const METER = 22;

    /** @var int Квадратный сантиметр */
    const SQUARE_CENTIMETER = 30;

    /** @var int Квадратный дециметр */
    const SQUARE_DECIMETER = 31;

    /** @var int Квадратный метр */
    const SQUARE_METER = 32;

    /** @var int Миллилитр */
    const MILLILITER = 40;

    /** @var int Литр */
    const LITER = 41;

    /** @var int Кубический метр */
    const CUBIC_METER = 42;

    /** @var int Киловатт час */
    const KILOWATT_HOUR = 50;

    /** @var int Гигакалория */
    const GIGACALORIE = 51;

    /** @var int Сутки (день) */
    const DAY = 70;

    /** @var int Час */
    const HOUR = 71;

    /** @var int Минута */
    const MINUTE = 72;

    /** @var int Секунда */
    const SECOND = 73;

    /** @var int Килобайт */
    const KILOBYTE = 80;

    /** @var int Мегабайт */
    const MEGABYTE = 81;

    /** @var int Гигабайт */
    const GIGABYTE = 82;

    /** @var int Терабайт */
    const TERABYTE = 83;

    /** @var int Применяется при использовании иных единиц измерения */
    const OTHER = 255;

}
