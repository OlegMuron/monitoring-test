<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class SiteVisit extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'site_visits';

    protected $fillable = [
        'id', 'url', 'ip_address', 'user_agent'
    ];
}
