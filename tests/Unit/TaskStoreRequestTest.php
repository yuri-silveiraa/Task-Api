<?php

namespace Tests\Unit;

use App\Http\Requests\TaskStoreRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class TaskStoreRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorize_returns_true()
    {
        $request = new TaskStoreRequest;
        $result = $request->authorize();

        $this->assertTrue($result);
    }

    public function test_rules_returns_expected_validation_rules()
    {
        $request = new TaskStoreRequest;

        $rules = $request->rules();

        $expectedRules = [
            'title' => 'required|string|min:3',
            'description' => 'nullable|string',
        ];

        $this->assertEquals($expectedRules, $rules);
    }

    public function test_title_is_required()
    {
        $request = new TaskStoreRequest;

        $rules = $request->rules();
        $validator = Validator::make(['description' => 'Description without title'], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->failed());
    }

    public function test_title_must_be_minimum_3_characters()
    {
        $request = new TaskStoreRequest;

        $rules = $request->rules();
        $validator = Validator::make(['title' => 'ab'], $rules);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('title', $validator->failed());
    }

    public function test_description_is_optional()
    {
        $request = new TaskStoreRequest;

        $rules = $request->rules();
        $validator = Validator::make(['title' => 'Valid title'], $rules);

        $this->assertTrue($validator->passes());
    }

    public function test_valid_data_passes_validation()
    {
        $request = new TaskStoreRequest;

        $rules = $request->rules();
        $validator = Validator::make([
            'title' => 'Valid task title',
            'description' => 'Valid task description',
        ], $rules);

        $this->assertTrue($validator->passes());
    }
}
