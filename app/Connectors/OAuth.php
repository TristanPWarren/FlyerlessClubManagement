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
    public function request($method, $uri, array $apiOptions = [])
    {
        $options = array();
        $options['base_uri'] = 'https://dev.flyerless.co.uk/API/';
        $options['form_params']['API_token'] = $this->getAccessToken();
        $options['form_params'] = array_merge($options['form_params'], $apiOptions);
        return $this->client->request($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function test(): bool
    {
        try {
            $options = ['Request_Type' => 0];
            $response = $this->request('POST', '', $options);
            $response = json_decode($response->getBody());
            if ($response->Authorised === "True") {
                return true;
            } else {
                return false;
            }
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

    private function getAccessToken(): string
    {
        //Get authModel if it exists
        try {
            $api_key = $this->getSetting('api_key');
            $authModel = AuthModel::where('api_key', '=', $api_key)->firstOrFail();

            if ($authModel->isTokenValid()) {
                return $authModel->access_token;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            //AuthModel not found
            $authModel = null;
        }

        try {
            return $this->refreshAccessToken($authModel);
        } catch (Exception $e) {
            throw new \Exception('Flyerless API Token could not be refreshed');
        }

    }


    private function refreshAccessToken(?AuthModel $authModel)
    {
        //Create new AuthModel if one doesn't exist
        if ($authModel === null) {
            $authModel = AuthModel::create([
                'api_key' => $this->getSetting('api_key')
            ]);
        }

        //Get token from flyerless
        $options = [];

        $options['base_uri'] = 'https://dev.flyerless.co.uk/API/';
        $options['form_params'] = [];
        $options['form_params']['API_KEY'] = $this->getSetting('api_key');

        try {
            $tokenResponse = $this->client->request('POST', '', $options);
        } catch (\Exception $e) {
            dd($e);
        }

        //Add token to authModel
        $authModel->access_token = json_decode($tokenResponse->getBody()->getContents())->Token;

        //Add Date to authModel
        $authModel->expires_at = Carbon::now()->addMinutes(25);

    	$authModel->save();

    	// Return the new access token
    	return $authModel->access_token;
    }

}