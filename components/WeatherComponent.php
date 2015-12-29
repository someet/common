<?php
namespace app\components;

use yii\base\Component;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;

/**
 * Class WeatherComponent
 * 参考 : http://www.heweather.com/documents/api
 * @package app\components
 */
class WeatherComponent extends Component
{
    /**
     * read only
     * @var
     */
    public $key;

    const WEATHER_FIXED_URL = "https://api.heweather.com/x3/weather";

    private $urlFormat;

    public function init()
    {
        parent::init();

        if (!$this->key) {
            throw new InvalidConfigException('key can not be blank');
        }

        $this->urlFormat = self::WEATHER_FIXED_URL
            . '?cityid='
            . '%s'
            . '&key='
            . $this->key;
    }

    public function getKey()
    {
        return $this->key;
    }

    /**
     * 获取天气情况
     * @param null $areaId string 区域ID,例如北京是CN101010100
     * @return array
     */
    public function getWeather($cityid = null)
    {
        $cityid = null === $cityid ? Yii::$app->params['weather.cityid'] : $cityid;

        $url = sprintf($this->urlFormat, $cityid);

        try {
            $ch = curl_init();
            // 执行HTTP请求
            curl_setopt($ch , CURLOPT_URL , $url);
            curl_setopt($ch , CURLOPT_SSL_VERIFYPEER , false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $res = curl_exec($ch);
            $weather = json_decode($res, true);

            if (isset($weather['HeWeather data service 3.0'][0]['aqi']['city']['pm25']) && isset($weather["HeWeather data service 3.0"][0]["now"]['tmp'])) {
                $pm25 = $weather["HeWeather data service 3.0"][0]["aqi"]['city']["pm25"];
                $temperature = $weather["HeWeather data service 3.0"][0]["now"]['tmp'];
            } else {
                Yii::error('获取天气情况发生异常,请检查, 返回内容为: '.$res);
                return [
                    'success' => 0,
                ];
            }
        } catch (Exception $e) {

            Yii::error('获取天气情况发生异常,请检查, '
                        . '文件: ' . $e->getFile()
                        . '行: ' . $e->getLine()
                        . '消息: ' . $e->getMessage()
                        . 'Code: ' . $e->getCode()
                        . '名称:' . $e->getName() );

            return [
               'success' => 0,
            ];
        }

        return [
            'success' => 1,
            'pm25' => $pm25,
            'temperature' => $temperature,
        ];
    }

}
