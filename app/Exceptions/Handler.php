<?php

namespace App\Exceptions;

use App\Domain\Exceptions\DomainException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

/**
 * Global Exception Handler
 *
 * Handles all exceptions in the application with proper logging,
 * reporting, and response formatting. Provides consistent error
 * responses for both web and API requests.
 *
 * @author Farm ERP Team
 *
 * @since 1.0.0
 */
class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \App\Domain\Exceptions\BatchNotFoundException::class,
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            $this->logException($e);
        });

        // Handle domain exceptions
        $this->renderable(function (DomainException $e, Request $request) {
            return $this->handleDomainException($e, $request);
        });

        // Handle validation exceptions with enhanced formatting
        $this->renderable(function (ValidationException $e, Request $request) {
            return $this->handleValidationException($e, $request);
        });

        // Handle HTTP exceptions
        $this->renderable(function (HttpException $e, Request $request) {
            return $this->handleHttpException($e, $request);
        });
    }

    /**
     * Report or log an exception with enhanced context
     */
    public function report(Throwable $e): void
    {
        // Don't report domain exceptions that shouldn't be reported
        if ($e instanceof DomainException && ! $e->shouldReport()) {
            return;
        }

        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e): Response
    {
        // Handle API requests differently
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->renderApiException($e, $request);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle domain exceptions with proper formatting
     */
    protected function handleDomainException(DomainException $e, Request $request): JsonResponse|Response
    {
        $this->logDomainException($e, $request);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json($e->toArray(), $e->getHttpStatusCode())
                ->withHeaders($this->getSecurityHeaders());
        }

        // For web requests, redirect back with error message
        return redirect()->back()
            ->withErrors(['error' => $e->getUserMessage()])
            ->withInput($request->except($this->dontFlash));
    }

    /**
     * Handle validation exceptions with enhanced formatting
     */
    protected function handleValidationException(ValidationException $e, Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => [
                    'type' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
                ->withHeaders($this->getSecurityHeaders());
        }

        return parent::render($request, $e);
    }

    /**
     * Handle HTTP exceptions
     */
    protected function handleHttpException(HttpException $e, Request $request): JsonResponse|Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'error' => [
                    'type' => 'HTTP_ERROR',
                    'message' => $this->getHttpErrorMessage($e->getStatusCode()),
                    'status_code' => $e->getStatusCode(),
                ],
            ], $e->getStatusCode())
                ->withHeaders($this->getSecurityHeaders());
        }

        return parent::render($request, $e);
    }

    /**
     * Render API exceptions with consistent formatting
     */
    protected function renderApiException(Throwable $e, Request $request): JsonResponse
    {
        // Domain exceptions
        if ($e instanceof DomainException) {
            return response()->json($e->toArray(), $e->getHttpStatusCode())
                ->withHeaders($this->getSecurityHeaders());
        }

        // Validation exceptions
        if ($e instanceof ValidationException) {
            return response()->json([
                'error' => [
                    'type' => 'VALIDATION_ERROR',
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
                ->withHeaders($this->getSecurityHeaders());
        }

        // HTTP exceptions
        if ($e instanceof HttpException) {
            return response()->json([
                'error' => [
                    'type' => 'HTTP_ERROR',
                    'message' => $this->getHttpErrorMessage($e->getStatusCode()),
                    'status_code' => $e->getStatusCode(),
                ],
            ], $e->getStatusCode())
                ->withHeaders($this->getSecurityHeaders());
        }

        // Generic server errors
        $statusCode = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;

        return response()->json([
            'error' => [
                'type' => 'SERVER_ERROR',
                'message' => app()->environment('production')
                    ? 'An unexpected error occurred. Please try again later.'
                    : $e->getMessage(),
                'status_code' => $statusCode,
            ],
        ], $statusCode)
            ->withHeaders($this->getSecurityHeaders());
    }

    /**
     * Log exceptions with enhanced context
     */
    protected function logException(Throwable $e): void
    {
        $context = [
            'exception' => get_class($e),
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'user_id' => auth()->id(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Add domain-specific context
        if ($e instanceof DomainException) {
            $context = array_merge($context, $e->getContext());
        }

        // Log with appropriate level based on severity
        $level = $this->getLogLevel($e);
        Log::log($level, 'Exception occurred', $context);
    }

    /**
     * Log domain exceptions with specific context
     */
    protected function logDomainException(DomainException $e, Request $request): void
    {
        if (! $e->shouldReport()) {
            return;
        }

        $context = array_merge($e->getLogData(), [
            'user_id' => $request->user()?->id,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'request_data' => $request->except($this->dontFlash),
        ]);

        Log::warning('Domain exception: '.$e->getMessage(), $context);
    }

    /**
     * Get appropriate log level for exception type
     */
    protected function getLogLevel(Throwable $e): string
    {
        return match (true) {
            $e instanceof DomainException && ! $e->shouldReport() => 'info',
            $e instanceof ValidationException => 'info',
            $e instanceof HttpException && $e->getStatusCode() < 500 => 'warning',
            default => 'error',
        };
    }

    /**
     * Get user-friendly HTTP error messages
     */
    protected function getHttpErrorMessage(int $statusCode): string
    {
        return match ($statusCode) {
            400 => 'Bad request. Please check your input and try again.',
            401 => 'Authentication required. Please log in to continue.',
            403 => 'You do not have permission to access this resource.',
            404 => 'The requested resource was not found.',
            405 => 'This method is not allowed for this resource.',
            409 => 'There was a conflict with the current state of the resource.',
            422 => 'The provided data could not be processed.',
            429 => 'Too many requests. Please slow down and try again later.',
            500 => 'An internal server error occurred. Please try again later.',
            502 => 'Bad gateway. The server is temporarily unavailable.',
            503 => 'Service temporarily unavailable. Please try again later.',
            504 => 'Gateway timeout. The request took too long to process.',
            default => 'An error occurred while processing your request.',
        };
    }

    /**
     * Get security headers for responses
     */
    protected function getSecurityHeaders(): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
        ];
    }

    /**
     * Convert validation exception to array for consistent API response
     */
    protected function invalidJson($request, ValidationException $exception): JsonResponse
    {
        return response()->json([
            'error' => [
                'type' => 'VALIDATION_ERROR',
                'message' => $exception->getMessage(),
                'errors' => $exception->errors(),
            ],
        ], $exception->status);
    }

    /**
     * Convert authentication exception to array for API response
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'type' => 'AUTHENTICATION_ERROR',
                    'message' => 'Unauthenticated. Please log in to continue.',
                ],
            ], 401)
                ->withHeaders($this->getSecurityHeaders());
        }

        return redirect()->guest($exception->redirectTo() ?? route('login'));
    }

    /**
     * Handle throttle exceptions
     */
    protected function throttled($request, \Illuminate\Http\Exceptions\ThrottleRequestsException $exception): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => [
                    'type' => 'RATE_LIMIT_ERROR',
                    'message' => 'Too many requests. Please slow down and try again.',
                    'retry_after' => $exception->getHeaders()['Retry-After'] ?? null,
                ],
            ], 429)
                ->withHeaders(array_merge(
                    $exception->getHeaders(),
                    $this->getSecurityHeaders()
                ));
        }

        return redirect()->back()
            ->withErrors(['error' => 'Too many requests. Please wait before trying again.']);
    }

    /**
     * Determine if the exception should be reported with full context
     */
    protected function shouldReportWithContext(Throwable $e): bool
    {
        // Always report server errors with full context
        if ($e instanceof \Error || $e instanceof \ErrorException) {
            return true;
        }

        // Report HTTP 5xx errors with context
        if ($e instanceof HttpException && $e->getStatusCode() >= 500) {
            return true;
        }

        // Report domain exceptions that should be reported
        if ($e instanceof DomainException && $e->shouldReport()) {
            return true;
        }

        return false;
    }
}
