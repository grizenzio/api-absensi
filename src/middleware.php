<?php

use Slim\App;

return function (App $app) {

    // e.g: $app->add(new \Slim\Csrf\Guard);

    $app->add(function ($request, $response, $next) {

        $kunci = $request->getQueryParam("kunci");

        if (!isset($kunci)) {

            return $response->withJson(["status" => "Butuh kunci api untuk mengakses."], 401);
        }


        $sql = "SELECT api_key FROM akses WHERE api_key=:api_key";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":api_key" => $kunci]);

        if ($stmt->rowCount() > 0) {

            $result = $stmt->fetch();

            if ($kunci == $result["api_key"]) {

                return $response = $next($request, $response);
            }
        }

        return $response->withJson(["status" => "Akses ditolak."], 401);
    });
};
