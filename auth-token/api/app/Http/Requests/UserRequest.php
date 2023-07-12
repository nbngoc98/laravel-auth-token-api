<?php

namespace App\Http\Requests;

class UserRequest extends BaseFormRequest
{

    public function __construct(
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $routeName = $this->route()->getName();

        return match ($routeName) {
            'users.index' => $this->getFetchUsersRules(),
            'users.search' => $this->getSearchUsersRules(),
            default => [],
        };
    }

    /**
     * User update rules
     */
    private function getFetchUsersRules(): array
    {
        return [
            'sort' => ['nullable', 'in:asc,desc'],
            'sort_by' => ['nullable', 'string'],
            'limit' => ['nullable', 'int'],
            'page' => ['nullable', 'int'],
            'email' => ['nullable', 'email'],
            'name' => ['nullable', 'string'],
        ];
    }

    /**
     * User search rules
     */
    private function getSearchUsersRules(): array
    {
        return [
            'query' => ['required', 'string'],
        ];
    }

    /**
     * Set the email and username to lowercase
     */
    protected function prepareForValidation()
    {
        if ($this->has('email')) {
            $this->merge(['email' => strtolower($this->get('email'))]);
        }

        if ($this->has('username')) {
            $this->merge(['username' => strtolower($this->get('username'))]);
        }
    }

    /**
     * Custom message for validation
     */
    public function messages(): array
    {
        return [

        ];
    }
}
