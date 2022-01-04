<?php


namespace Nichozuo\LaravelHelpers\Traits;


use Faker\Factory;
use Illuminate\Support\Str;

trait TestCaseTrait
{
    protected $token;
    protected $faker;
    protected $id;

    public function setUp(): void
    {
        $this->faker = Factory::create('zh_CN');
        parent::setUp();
    }

    /**
     * @intro 发起接口的请求
     * @param string $method
     * @param array $params
     * @param array $headers
     * @return void
     */
    protected function go(string $method, array $params = [], array $headers = [])
    {
        $url = $this->getUrl($method);
        $headers['Authorization'] = 'Bearer ' . $this->token;
        $response = $this->post($url, $params, $headers);
        $json = $response->json();
        $response->assertStatus(200);
        dump(json_encode($json));
        dump($json);
    }

    /**
     * @intro 获取fake图片信息
     * @param $number
     * @return false|string
     */
    private function getFakeImages($number)
    {
        $images = null;
        foreach (range(0, $number) as $index) {
            $images[] = [
                'uid' => uniqid(),
                'url' => $this->faker->imageUrl(),
                'name' => $this->faker->name,
                'loading' => false
            ];
        }
        return json_encode($images);
    }


    /**
     * @intro 通过testMethod方法名，获取接口url
     * @param string $method
     * @return string
     */
    private function getUrl(string $method): string
    {
        $t1 = explode('\\', $method);
        $urls[] = 'api';

        foreach ($t1 as $key => $value) {
            if ($key > 1 && $key < count($t1) - 1) {
                $urls[] = Str::snake($value);
            }
        }

        $urls[] = str_replace(
            '::test_',
            '',
            Str::snake(
                str_replace(
                    'ControllerTest',
                    '/',
                    end($t1)
                )
            )
        );

        return '/' . implode('/', $urls);
    }
}