<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse implements Responsable
{
    public function __construct(
        protected string $message,
        protected mixed $data,
        protected int $status = Response::HTTP_OK
    ) {}

    public static function __callStatic($name, $arguments)
     {
         $data = $arguments[0] ?? null;
         
         if (str_ends_with($name, 'Collection')) {
             $entity = substr($name, 0, -10);

             if (!($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection)) {
                 $resourceClass = 'App\\Http\\Resources\\' . ucfirst($entity) . 'Resource';

                 if (!class_exists($resourceClass)) {
                     throw new \BadMethodCallException(
                         "Resource class '{$resourceClass}' not found for method '{$name}'. " .
                         "Please check your method name or create the corresponding Resource."
                     );
                 }
                 
                 $data = $resourceClass::collection($data);
             }
             
             return self::context($entity, $data);
         }
 
         $entity = $name;
         if (!($data instanceof \Illuminate\Http\Resources\Json\JsonResource)) {
             $resourceClass = 'App\\Http\\Resources\\' . ucfirst($entity) . 'Resource';

             if (!class_exists($resourceClass)) {
                 throw new \BadMethodCallException(
                     "Resource class '{$resourceClass}' not found for method '{$name}'. " .
                     "Please check your method name or create the corresponding Resource."
                 );
             }
             
             $data = new $resourceClass($data);
         }
         
         return self::context($entity, $data);
     }

    public static function success(mixed $data = [], string $message = 'Succès'): self
    {
        return new self($message, $data, Response::HTTP_OK);
    }

    public static function created(mixed $data = [], string $message = 'Créé'): self
    {
        return new self($message, $data, Response::HTTP_CREATED);
    }

    public static function noContent(): self
    {
        return new self(message: '', data: [], status: Response::HTTP_NO_CONTENT);
    }

    public static function notFound(string $message = ''): self
    {
        return new self($message, data: [], status: Response::HTTP_NOT_FOUND);
    }

    public static function context(string $entity, mixed $data = []): self
    {
        if ($data instanceof \Illuminate\Http\Resources\Json\ResourceCollection) {
            if ($data->collection->isEmpty()) {
                return self::noContent();
            }
         }

         if ($data instanceof \Illuminate\Support\Collection) {
            if ($data->isEmpty()) {
                return self::noContent();
            }
        }

        if (is_array($data) && empty($data)) {
            return self::noContent();
        }

        $message = sprintf("%s resource successfully retrieved", ucfirst($entity));

        return self::success($data, $message);
    }

    public function render(): JsonResponse
    {
        $response = [];

        if ($this->message !== '') {
            $response['message'] = $this->message;
        }

        $response['data'] = ($this->data === [] || $this->data === null) ? null : $this->data;

        return new JsonResponse(empty($response) ? null : $response, $this->status);
    }

    public function toResponse($request): JsonResponse
    {
        return $this->render();
    }
}
