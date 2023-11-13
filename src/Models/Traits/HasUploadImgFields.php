<?php

namespace Ericli1018\AwesomeFieldsForBackpack\Models\Traits;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/*
|--------------------------------------------------------------------------
| Methods for storing uploaded files (used in CRUD).
|--------------------------------------------------------------------------
*/
trait HasUploadImgFields
{
    /**
     * Handle multiple file upload and DB storage:
     * - if files are sent
     *     - stores the files at the destination path
     *     - generates random names
     *     - stores the full path in the DB, as JSON array;
     * - if a hidden input is sent to clear one or more files
     *     - deletes the file
     *     - removes that file from the DB.
     *
     * @param  string  $value  Value for that column sent from the input.
     * @param  string  $attribute_name  Model attribute name (and column in the db).
     * @param  string  $disk  Filesystem disk used to store files.
     * @param  string  $destination_path  Path in disk where to store the files.
     */
    public function uploadImgMultipleFilesToDisk($value, $attribute_name, $disk, $destination_path)
    {
        $originalModelValue = $this->getOriginal()[$attribute_name] ?? [];

        if (!is_array($originalModelValue)) {
            $attribute_value = json_decode($originalModelValue, true) ?? [];
        } else {
            $attribute_value = $originalModelValue;
        }

        $files_to_clear = request()->get('clear_'.$attribute_name);
        
        $files_meta = request()->get('meta_'.$attribute_name);
        if ($files_meta) {
            $files_meta = json_decode($files_meta, true) ?? [];
        } else {
            $files_meta = $attribute_value;
        }
        
        // if a file has been marked for removal,
        // delete it from the disk and from the db
        if ($files_to_clear) {
            foreach ($files_to_clear as $key => $filename) {
                \Storage::disk($disk)->delete($filename);
                $files_meta = Arr::where($files_meta, function ($value, $key) use ($filename) {
                    return $value['file_path'] != $filename;
                });
            }
        }

        // if a new file is uploaded, store it on disk and its filename in the database
        if (request()->hasFile($attribute_name)) {
            foreach (request()->file($attribute_name) as $file) {
                if ($file->isValid()) {
                    // 1. Generate a new file name
                    $file_name = $file->getClientOriginalName();
                    $new_file_name = md5($file->getClientOriginalName().random_int(1, 9999).time()) . '.' . $file->getClientOriginalExtension();

                    // 2. Move the new file to the correct path
                    $file_path = $file->storeAs($destination_path, $new_file_name, $disk);

                    // 3. Add the public path to the database
                    $files_meta_values = Arr::where($files_meta, function ($value, $key) use ($files_meta, $file_name) {
                        return $value['file_path'] == $file_name;
                    });
                    if (count($files_meta_values) > 0) {
                        foreach($files_meta_values as $key => $item) {
                            $files_meta[$key]['file_path'] = $file_path;
                        }
                    } else {
                        $files_meta[] = ['file_path' => $file_path, 'comment' => '', 'is_selected' => false, 'fn' => $file_name];
                    }
                }
            }
        }
        $this->attributes[$attribute_name] = json_encode($files_meta);
    }
}
