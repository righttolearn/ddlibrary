<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 * @method static findOrFail(int $id)
 *
 * @property mixed en (English)
 * @property mixed fa (Farsi)
 * @property mixed ps (Pashto)
 * @property mixed mj (Munji)
 * @property mixed no (Nuristani)
 * @property mixed pa (Pashayi)
 * @property mixed sh (Shughni)
 * @property mixed sw (Swahili)
 * @property mixed uz (Uzbek)
 */
class GlossarySubject extends Model
{
    use HasFactory;
    protected $table = 'glossary_subjects';
}
