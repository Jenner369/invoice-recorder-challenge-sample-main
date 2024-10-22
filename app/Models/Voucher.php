<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string $id
 * @property string $issuer_name
 * @property string $issuer_document_type
 * @property string $issuer_document_number
 * @property string $receiver_name
 * @property string $receiver_document_type
 * @property string $receiver_document_number
 * @property float $total_amount
 * @property string $xml_content
 * @property string $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @property-read Collection|User[] $lines
 * @mixin Builder
 */
class Voucher extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $fillable = [
        'issuer_name',
        'issuer_document_type',
        'issuer_document_number',
        'receiver_name',
        'receiver_document_type',
        'receiver_document_number',
        'total_amount',
        'xml_content',
        'series',
        'number',
        'voucher_type',
        'currency',
        'user_id',
    ];

    protected $casts = [
        'total_amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(VoucherLine::class);
    }

    public function scopeDuplicate(
        Builder $query, 
        string $issuerDocumentNumber, 
        string $issuer_document_type, 
        string $totalAmount,
        string $series,
        string $number,
        string $voucherType
        ): Builder
    {
        return $query->where('issuer_document_number', $issuerDocumentNumber)
            ->where('issuer_document_type', $issuer_document_type)
            ->where('total_amount', $totalAmount)
            ->where('series', $series)
            ->where('number', $number)
            ->where('voucher_type', $voucherType);
    }        

    public function scopeByUserId(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeBySeries(Builder $query, string $series): Builder
    {
        return $query->where('series', $series);
    }

    public function scopeByNumber(Builder $query, string $number): Builder
    {
        return $query->where('number', $number);
    }

    public function scopeFromDate(Builder $query, Carbon $from): Builder
    {
        return $query->where('created_at', '>=', $from);
    }

    public function scopeToDate(Builder $query, Carbon $to): Builder
    {
        return $query->where('created_at', '<=', $to);
    }

}
