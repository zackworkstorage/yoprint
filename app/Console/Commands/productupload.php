<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductFile;
use App\Models\ProductDetail;
use DB;

class productupload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:productupload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product upload cronjob';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        $allowed_files = [
            'unique_key',
            'product_title',
            'product_description',
            'style',
            'sanmar_mainframe_color',
            'size',
            'color_name',
            'piece_price'
        ];
        $approved_key = [];
        $handle = '';
        
        $results = ProductFile::where('status', ProductFile::STATUS_PENDING)->get()->all();
        if(!empty($results)){
            foreach($results as $result){
//                ProductFile::where('id', $result->id)->update([
//                    'status' => ProductFile::STATUS_PROCESSING
//                ]);
                
                try{
                    DB::beginTransaction();
                    if (($handle = fopen($result->filepath, "r")) !== FALSE) {
                        while (($fields = fgetcsv($handle, null, ",")) !== FALSE) {
                            foreach($fields as $key => $field){
                                foreach($allowed_files as $allowed_file){
                                    $str = preg_replace("/[^a-zA-Z0-9-_]+/", "", $field);
                                    if(strtolower(trim($str)) == $allowed_file){
                                        $approved_key[strtolower(trim($str))] = $key;
                                    }
                                }
                            }
                            break;
                        }
                        
                        if(!empty($approved_key)){
                            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                                $product_title = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', html_entity_decode($data[$approved_key['product_title']], ENT_COMPAT, 'UTF-8'));
                                $product_description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', html_entity_decode($data[$approved_key['product_description']], ENT_COMPAT, 'UTF-8'));
                                $style = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', html_entity_decode($data[$approved_key['style']], ENT_COMPAT, 'UTF-8'));
                                $sanmar_mainframe_color = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', html_entity_decode($data[$approved_key['sanmar_mainframe_color']], ENT_COMPAT, 'UTF-8'));
                                $color_name = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', html_entity_decode($data[$approved_key['color_name']], ENT_COMPAT, 'UTF-8'));
                                
                                ProductDetail::updateOrCreate([
                                    'unique_key' => $data[$approved_key['unique_key']],
                                ], [
                                    'unique_key' => $data[$approved_key['unique_key']],
                                    'product_file_id' => $result->id,
                                    'product_title' => $product_title,
                                    'product_description' => $product_description,
                                    'style' => $style,
                                    'sanmar_mainframe_color' => $sanmar_mainframe_color,
                                    'size' => $data[$approved_key['size']],
                                    'color_name' => $color_name,
                                    'piece_price' => $data[$approved_key['piece_price']],
                                ]);
                            }
                            ProductFile::where('id', $result->id)->update([
                                'status' => ProductFile::STATUS_COMPLETED,
                                'remark' => 'Completed'
                            ]);
                        }
                        else{
                            ProductFile::where('id', $result->id)->update([
                                'status' => ProductFile::STATUS_FAILED,
                                'remark' => 'Empty data'
                            ]);
                        }
                        fclose($handle);
                    }
                    DB::commit();
                }
                catch (\Exception $e) {
                    DB::rollback();
                    ProductFile::where('id', $result->id)->update([
                        'status' => ProductFile::STATUS_FAILED,
                        'remark' => 'Something wrong'
                    ]);
                    
                    if(!empty($handle)){
                        fclose($handle);
                    }
                }
            }
        }
    }
}
