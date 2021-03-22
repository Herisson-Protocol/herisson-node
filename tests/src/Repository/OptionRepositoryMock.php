<?php


namespace Herisson\Repository;


use Herisson\Entity\HerissonEntityInterface;
use Herisson\Entity\Option;

class OptionRepositoryMock extends HerissonRepositoryMock implements OptionRepositoryInterface
{
    protected $fields = ['id', 'name', 'value'];
    protected $objects = [
        1 => ['id' => 1, 'name' => 'sitename', 'value' => 'HerissonSite'],
        2 => ['id' => 2, 'name' => 'email', 'value' => 'admin@example.org'],
        3 => ['id' => 3, 'name' => 'publicKey', 'value' => '-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyzdfdV2jjKoiyBm2oN6m
HUpGsXm1yX6X6FLQjMRUoL+NmOXcpVQeoh1AKjHtvAOouSShAGtqLID5fVF7Vhni
U6l8sEioPBAeVVEZ7jy/Wra2VNulhsoM9pWyTwbjz31zNHHYh3Q089DxJHYuE66a
Xst8k2lhKQDnPBOcJ6lKmjmj2u7nTk6dGm3Bkws3cBXXmluU1N0Azg4HVOiSTtZA
XAI9ugIz5zXl9nhR2rd+ZWBbvygeciTLdooTu9D/U6/i7A8rYHd5R2BABAWuHnFe
BKmZLMbVASjJqvEXuj0CHqSEcxJfnSB/DgWLAbX61+sDU2jpa7T6x4dA1cocIe7u
8wIDAQAB
-----END PUBLIC KEY-----'],
        4 => ['id' => 4, 'name' => 'privateKey', 'value' => '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCwiTV+yYwEAD6M
ap6tj05BH6e2FZ5jipk1J1ntIUuH7k9WU8fI/d3Rw3jaMfNH/c2fl3Me4QdFjk1M
6F1yIN+QYregd/X+CJTFpNLPG9jLsjI/hwHFELaU889qmCj5SUbao8u/PqCa0P/L
hSG62T75bWi89Sdw3UVF/ApV2vFGwzT3JfkxDQcLMdsg7kzPJ0ctBHCDb1qG8fKs
iikI2A1rgGHcj/L83TkvxHVAWC49rKLaPffNBmmslzIb3/tOHXmfCiFVQdK0gjf6
bivlhFggYEHF5CEhQQTrOpitmOlNfGj9G1j+dKhvhreOtDuVhWjMy/a22P0M7ypU
hbMPs0U5AgMBAAECggEAbabcLoN7z6RjNY6oPv/LSJIj9uqYthWJskVeCvdqVeVF
LEbfyk09caRrtYPfK7FO/jjxDZYSkTahdwrBuDkJBEL88dRxXDfySI1nT7NpRqTJ
p0HxbHlEH2MNS5V1pdnJA9dHn+ODmIYGzGBGIiv8fKtjqjLQK7+U3rJ9rPRQd3WR
f32Dl2zvbrnsok8o+LCR5sGbWcx9jC2VwRMqV6z8G3OAu8KTr1h7lwdGlOc4sXBF
WM1IdGuABVcQq9Yl09ceryhbjyhOHO90omUdh/4Xr4iWzNM/5JOm8pwNyi1GEIM8
Up2AFYI5RzeRhF6+JKkZc4ydNxNPtSROVjnrwj1EAQKBgQDpOfNeQtOzw4CrqQ74
CpYY0soYYiXn6X15Ny2UVZ3liM/8zDBrg8m45A/bKedXphK00f/aDMOdTI7Gthk2
bScfrwN/dDaGORe03hOKaVbtUygxauOlVt4q7IjZ1THKnwYtQMCp8a+lMRAAwQyS
NDnhMJfp4psINXH+C25FNIsOgQKBgQDBxiWJVbJ/6gLhrHx0xdCwOkVmPVlGSX3l
Q5LIIzeEDvCXE9Yb3XHw7pf72/SsgMq4UzOjK7TEr1QRZgXZKQ06Ef3frr8u5D3B
1JGFMnl5/Dn0aVpL26u19ucFpmS9u+fPl/5FZ01GP7W632NJPW2gjDKi+IztQsbM
vfY3w8TKuQKBgA/X4/RvTbpUeZUvstiev3uINjpZ0IFklyV43hvJhSRmmtptdIyZ
M0bgF0OoIRMPMQ4fheXmIBO7c6eED0pnN9UrKm2qE1oi3r1mqKUqasFMeNqCjFxa
/lSkJNfHZ85/5weD8pKY3Hm4T4H9m0EDUzs89dTpk+aG2uuLEz6YRyoBAoGBAJmp
CacoYA5zgal34JGxNFYrP1FeIOdN7BncRg+Tbbi5KCyFvGoIZwyKB9ffit0onJki
0XX5eLXn2yCY6NZWaXi9P7cbmdwTfq2wZV1cs1v6mfKpgMNbBYWcr4KZepXC5oaG
n0pmKq8kYnY4I0G3BrIjuxvlQcv8Ai3mDdQW2H1xAoGBAJYUaF8RljMIbi0yO26z
Fg0Jo6MHFSUlsxtUJTA8ciiKzHWTjr/Q4HU0GSaSTRp6t6CBR5jF4GMD0NAVnLaO
iY5ZfU7W8Rib10I+BTlZfieqveaHJcO5QjsM4C4V+yErWhKCMfFKsbIGMI9GyQn0
ejy55+qC4fvho+VipIv9P4T1
-----END PRIVATE KEY-----'],
        5 => ['id' => 5, 'name' => 'siteurl', 'value' => 'http://localhost:8000'],
        6 => ['id' => 6, 'name' => 'basePath', 'value' => 'bookmarks'],
    ];

    public function createFromData($objectData) : HerissonEntityInterface
    {
        $option = new Option();
        $option->setId($objectData['id']);
        $option->setName($objectData['name']);
        $option->setValue($objectData['value']);
        return $option;
    }

}