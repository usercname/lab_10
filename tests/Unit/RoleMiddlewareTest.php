<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RoleMiddlewareTest extends TestCase
{
    public function test_middleware_class_exists()
    {
        $this->assertTrue(class_exists(RoleMiddleware::class));
    }

    public function test_middleware_instantiation()
    {
        $middleware = new RoleMiddleware();
        $this->assertInstanceOf(RoleMiddleware::class, $middleware);
    }

    public function test_middleware_handle_method_execution()
    {
        $middleware = new RoleMiddleware();
        $request = Request::create('/');
        
        // Просто проверяем, что метод handle вызывается и возвращает Response или Closure
        // В Unit-тестах middleware тестируется через выполнение, без роутера
        $next = fn($req) => new Response('OK');
        $response = $middleware->handle($request, $next);
        
        $this->assertInstanceOf(Response::class, $response);
    }
}