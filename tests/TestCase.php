<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param string $url
     * @param array $data
     * @return TestResponse
     */
    protected function postData(string $url, array $data): TestResponse
    {
        return $this->post("/api/v1/$url", $data);
    }

    protected function getData(string $url): TestResponse
    {
        return $this->get("/api/v1/$url");
    }

    protected function deleteData(string $url): TestResponse
    {
        return $this->delete("/api/v1/$url");
    }
}
