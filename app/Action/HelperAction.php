<?php

namespace App\Action;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;

class HelperAction
{
    const APP_NME = 'ADBARTA';
    const ROLE_VENDOR = 'vendor';
    const ROLE_USER = 'user';
    const ROLE_ADMIN = 'admin';
    const APP_LOCAL_LANG = 'sv';
    const VENDOR_STATUS_APPROVED = 'approved';
    const LOCAL_IMAGE_URL = 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOH2aZnIHWjMQj2lQUOWIL2f4Hljgab0ecZQ&usqp=CAU';
    const PRODUCT_STATUS_APPROVED = 'approved';
    const PRODUCT_STATUS_SOLD = 'sold';
    const PRODUCT_STATUS_NOT_APPROVED = 'not_approved';
    const PRODUCT_STATUS_NOT_PENDING = 'pending';

    public static function validationResponse($message): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'error' => true,
                'message' => $message,
                'data' => null,
            ],
            422
        );
    }
    public static function errorResponse($message): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'error' => true,
                'message' => $message,
                'data' => null,
            ],
            422
        );
    }
    public static function successResponse($message, $data): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            [
                'error' => false,
                'message' => $message,
                'data' => $data,
            ],
            200
        );
    }

    public static function jsonResponse($data): JsonResponse
    {
        return response()->json($data);
    }

    public static function serviceResponse($error, $message, $data )
    {
        return [
            'error' => $error,
            'message' => $message,
            'data' => $data,
        ];
    }
    public static function splitWord($string): array
    {
        $names = explode(' ', trim($string));

        $first_name = '';
        $lastNames = [];
        //$names = Helper::spitWord($request->name);

        foreach ($names as $key => $name) {
            if ($key === 0) {
                $first_name = $name;
            } else {
                $lastNames[] = $name;
            }
        }
        $last_name = implode(' ', $lastNames);
        return compact('first_name', 'last_name');
    }
    public static function save_image($image, $directory): string
    {
        if (env('APP_ENV') === 'local') {
            return self::LOCAL_IMAGE_URL;
        }
        $path = "$directory";
        $fileType = $image->getClientOriginalExtension();
        $imageName = rand() . '.' . $fileType;
        $path_info = pathinfo($imageName, PATHINFO_EXTENSION);
        /**
         * This is to save on storage folder
         */
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath());
//            $img->resize(240, 180, function ($constraint) {
//                $constraint->aspectRatio();
//            });
        $img->stream();
        $imageUrl ='public/'.$path . '/' . $fileName;
//        Storage::disk('local')
//            ->put($imageUrl, $img);

//        $photo = Image::make($image)
//            ->resize(400, null, function ($constraint) { $constraint->aspectRatio(); } )
//            ->encode('jpg',80);

        Storage::disk('local')->put( $imageUrl, $img);
        $filePath = Storage::url($imageUrl);
        return asset($filePath);
    }

    public static function saveImage($image, $directory): string
    {
        if (env('APP_ENV') === 'local') {
            return self::LOCAL_IMAGE_URL;
        }
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath())->stream();
        $imageUrl = "public/{$directory}/{$fileName}";
        Storage::disk('local')->put($imageUrl, $img);
        return asset(Storage::url($imageUrl));
    }
    public static function saveVendorImage($image, $directory): string
    {
        if (env('APP_ENV') === 'local') {
            return self::LOCAL_IMAGE_URL;
        }
        $fileName = time() . '.' . $image->getClientOriginalExtension();
        $img = Image::make($image->getRealPath());
        $img->resize(399, 399)->stream();
        $imageUrl = "public/{$directory}/{$fileName}";
        Storage::disk('local')->put($imageUrl, $img);
        return asset(Storage::url($imageUrl));
    }
    /**
     * @throws ValidationException
     */
    public static function failedValidation(Validator $validator)
    {
        $response = new JsonResponse([
            'error' => true,
            'message' => $validator->errors()->first(),
            'data' => null,
            'errors' => $validator->errors()
        ], 422);

        throw new ValidationException($validator, $response);
    }

}
