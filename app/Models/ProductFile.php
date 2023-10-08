<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductFile extends Model
{
    const FILEPATH = 'files/';
    
    const STATUS_PENDING = 'Pending';
    const STATUS_PROCESSING = 'Processing';
    const STATUS_FAILED = 'Failed';
    const STATUS_COMPLETED = 'Completed';
    
    protected $table = 'product_file';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'filename',
        'filepath',
        'status',
        'remark'
    ];
    
    
    /**
     * Get the parent - banner
     */
    public function detail()
    {
        return $this->hasMany('App\Models\ProductDetail', 'id', 'product_file_id');
    }
    
    public static function timeElapsedString($datetime, $full = false) {
        $etime = time() - strtotime($datetime);

        if ($etime < 1)
        {
            return '0 seconds';
        }

        $a = array( 365 * 24 * 60 * 60  =>  'year',
                     30 * 24 * 60 * 60  =>  'month',
                          24 * 60 * 60  =>  'day',
                               60 * 60  =>  'hour',
                                    60  =>  'minute',
                                     1  =>  'second'
                    );
        $a_plural = array( 'year'   => 'years',
                           'month'  => 'months',
                           'day'    => 'days',
                           'hour'   => 'hours',
                           'minute' => 'minutes',
                           'second' => 'seconds'
                    );

        foreach ($a as $secs => $str)
        {
            $d = $etime / $secs;
            if ($d >= 1)
            {
                $r = round($d);
                return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
            }
        }
    }
}