<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ItemPhoto extends Model
{
    /** @use HasFactory<\Database\Factories\ItemPhotoFactory> */
    use HasFactory;

    protected $fillable = [
        'item_id',
        'path',
        'sort_order',
    ];

    /**
     * @return BelongsTo<Item, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Public URL for displaying the photo.
     */
    public function url(): string
    {
        return Storage::url($this->path);
    }
}
