<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    use ApiResponse;
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        if ($exception instanceof ModelNotFoundException) {
            $modelName = strtolower(class_basename($exception->getModel()));
            return $this->errorResponse("Does not exists any {$modelName} with the specified indentificator", 404);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof AuthorizationException) {
            return $this->errorResponse($exception->getMessage(), 403);
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->errorResponse('URL not found', 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return $this->errorResponse('Method for request not found', 404);
        }

        if ($exception instanceof HttpException) {
            return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
        }

        if ($exception instanceof QueryException) {
            $errorCode = $exception->errorInfo[1];
            if ($errorCode == 1451) {
                return $this->errorResponse('Cant not remove this resource permanently. It is related with any other resource', 409);
            }
        }

        if($exception  instanceof TokenMismatchException){
            return redirect()->back()->withInput($request->input());
        }

        //  if(config('app.debug'))
        //     return $this->errorResponse('Unexpected Exception. Try again', 500);
        return parent::render($request, $exception);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errors = $e->validator->errors()->getMessages();
        if($this->isFrontend($request)){
            return $request->ajax() ? response()->json($errors, 442) : redirect()->back()->withInput($request->input())->withErrors($errors);
        }
        return $this->errorResponse($errors, 422);
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($this->isFrontend($request)){
            return redirect()->guest('login');
        }
        return $this->errorResponse('Unauthenticated', 401);
    }

    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }
}
