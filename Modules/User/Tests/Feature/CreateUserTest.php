<?php

namespace Modules\User\Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\User\Entities\User;

class CreateUserTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /**
     * @test
     */
    public function get_all_user()
    {
        $res = $this->getData("users");
        $res->assertOk();
    }


    /**
     * @test
     */
    public function create_user()
    {
        $res = $this->postData("users", [
            'username' => "lok",
            "first_name" => "jack",
            "last_name" => "lol",
            "email" => "h@h.com",
            "password" => "123456",
            "confirme_password" => "123456"
        ]);
        $res->assertJsonPath("success", true);
        $res->assertCreated();
    }

    /**
     * @test
     */
    public function destroy_user()
    {

        $res = $this->deleteData("users/1");

        $res->assertOk();
        $res->assertJsonPath("success", true);
    }
}
