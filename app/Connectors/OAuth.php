<?php

namespace Flyerless\FlyerlessClubManagement\Connectors;

use Flyerless\FlyerlessClubManagement\Models\FlyerlessAuthCode as AuthModel;
use BristolSU\Support\Connection\Contracts\Connector;
use Carbon\Carbon;
use FormSchema\Generator\Field;
use FormSchema\Schema\Form;
use GuzzleHttp\Exception\GuzzleException;

class OAuth extends Connector
{

    /**
     * @inheritDoc
     */
    public function request($method, $uri, array $options = [])
    {
        $options['base_uri'] = config('typeform_service.base_uri');
        $headers = ((isset($options['headers']) && is_array($options['headers']))?$options['headers']:[]);
        $headers['Authorization'] = 'Bearer ' . $this->getAccessToken();
        $options['headers'] = $headers;
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function test(): bool
    {
        try {
            $this->request('get', '/me');
            return true;
        } catch (GuzzleException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    static public function settingsSchema(): Form
    {
        return \FormSchema\Generator\Form::make()->withGroup(
            \FormSchema\Generator\Group::make('Flyerless Api Key')->withField(
                \FormSchema\Generator\Field::input('api_key')->inputType('text')->required(true)
            )
        )->getSchema();
    }

    private function getAccessToken($refreshable = false): string
    {
        $authCode = AuthModel::findOrFail($this->getSetting('auth_code_id'));
        if($authCode->isValid()) {
            return $authCode->auth_code;
        }
        if($refreshable && $this->refreshAccessToken($authCode)) {
            return $this->getAccessToken(false);
        } else {
            // TODO Throw special error to send email to people
            throw new \Exception('Access token could not be refreshed');
        }
    }

}