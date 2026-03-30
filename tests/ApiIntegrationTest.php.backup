<?php

namespace Tests;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/FakeInfo.php';

#[Group('integration')]
class ApiIntegrationTest extends TestCase
{
    private static string $baseUrl;

    public static function setUpBeforeClass(): void
    {
        self::$baseUrl = rtrim(getenv('API_BASE_URL') ?: 'http://127.0.0.1', '/');
    }

    // Makes an HTTP request and returns the status code and decoded body.
    private function request(string $path, string $method = 'GET'): array
    {
        $context = stream_context_create([
            'http' => ['method' => $method, 'ignore_errors' => true, 'timeout' => 5],
        ]);
        $body = file_get_contents(self::$baseUrl . $path, false, $context);
        preg_match('/HTTP\/\S+ (\d+)/', $http_response_header[0], $matches);
        return [
            'status' => (int) $matches[1],
            'body'   => json_decode($body, true),
        ];
    }

    // Happy path: the HTTP layer delivers a valid birthDate.
    public function testBirthDateIsValid(): void
    {
        $response = $this->request('/name-gender-dob');
        $this->assertEquals(200, $response['status']);
        [$year, $month, $day] = array_map('intval', explode('-', $response['body']['birthDate']));
        $this->assertTrue(checkdate($month, $day, $year));
    }

    // A request to an unknown endpoint must return 404.
    public function testUnknownEndpointReturns404(): void
    {
        $response = $this->request('/nonexistent');
        $this->assertEquals(404, $response['status']);
    }

    // A POST request must return 405 since the API is GET-only.
    public function testPostMethodReturns405(): void
    {
        $response = $this->request('/name-gender-dob', 'POST');
        $this->assertEquals(405, $response['status']);
    }

    // An SQL injection attempt in a query parameter must not crash the server.
    public function testSqlInjectionDoesNotCrash(): void
    {
        $response = $this->request("/person?n=1' OR '1'='1");
        $this->assertNotEquals(500, $response['status']);
    }
}
