<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {

    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });

    // Rute GET /absensi/
    $app->get("/absensi/", function (Request $request, Response $response) {

        $sql = "SELECT * FROM absensi";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["error" => false, "absensi" => $result], 200);
    });


    // Rute GET /absensi/12345
    $app->get("/absensi/{nidn}", function (Request $request, Response $response, $args) {
        $nidn = $args["nidn"];
        $sql = "SELECT * FROM absensi WHERE nidn=:nidn";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([":nidn" => $nidn]);
        $result = $stmt->fetchAll();
        return $response->withJson(["error" => false, "absensi" => $result], 200);
    });


    // Rute GET /absensi/cari
    $app->get("/absensi/cari/", function (Request $request, Response $response, $args) {
        $huruf = $request->getQueryParam("huruf");
        $sql = "SELECT * FROM absensi WHERE nama LIKE '%$huruf%' OR nidn LIKE '%$huruf%'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $response->withJson(["error" => false, "absensi" => $result], 200);
    });


    // Rute POST /absensi (Menambah data absensi)
    $app->post("/absensi/", function (Request $request, Response $response) {

        $absen = $request->getParsedBody();

        $sql = "INSERT INTO absensi (id_absensi, nidn, nama, tanggal, jam, status, telat) VALUE (:id_absensi, :nidn, :nama, :tanggal, :jam, :status, :telat)";
        $stmt = $this->db->prepare($sql);

        $data = [

            ":id_absensi" => $absen["id_absensi"],
            ":nidn" => $absen["nidn"],
            ":nama" => $absen["nama"],
            ":tanggal" => $absen["tanggal"],
            ":jam" => $absen["jam"],
            ":status" => $absen["status"],
            ":telat" => $absen["telat"]
        ];

        $sql = "SELECT nidn, tanggal FROM absensi WHERE nidn=:nidn AND tanggal=:tanggal";
        $stmtcek = $this->db->prepare($sql);
        $stmtcek->execute([":nidn" => $absen["nidn"], ":tanggal" => $absen["tanggal"]]);

        //Jika ada
        if ($stmtcek->rowCount() > 0) {

            return $response->withJson(["error" => true, "pesan" => "Anda sudah absen hari ini."], 200);

        } else {

            if ($stmt->execute($data)) {

                return $response->withJson(["error" => false, "pesan" => "Absen berhasil."], 200);
            }
        }

        return $response->withJson(["error" => true, "pesan" => "Absen gagal."], 200);
    });


    // Rute PUT /absensi/12345 (Mengubah data absensi)
    $app->put("/absensi/{id_absensi}", function (Request $request, Response $response, $args) {
        $id_absensi = $args["id_absensi"];
        $absen = $request->getParsedBody();
        $sql = "UPDATE absensi SET nidn=:nidn, nama=:nama, tanggal=:tanggal, jam=:jam, status=:status, telat=:telat WHERE id_absensi=:id_absensi";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_absensi" => $id_absensi,
            ":nidn" => $absen["nidn"],
            ":nama" => $absen["nama"],
            ":tanggal" => $absen["tanggal"],
            ":jam" => $absen["jam"],
            ":status" => $absen["status"],
            ":telat" => $absen["telat"]
        ];

        $sql = "SELECT id_absensi FROM absensi WHERE id_absensi=:id_absensi";
        $stmtcek = $this->db->prepare($sql);
        $stmtcek->execute([":id_absensi" => $id_absensi]);

        //Jika ada
        if (!$stmtcek->rowCount() > 0) {

            return $response->withJson(["error" => true, "pesan" => "Data tidak ditemukan."], 200);
        } else {

            if ($stmt->execute($data)) {

                return $response->withJson(["error" => false, "pesan" => "Ubah data berhasil."], 200);
            }
        }

        return $response->withJson(["error" => true, "pesan" => "Data tidak ditemukan."], 200);
    });


    // Rute DELETE /absensi/1
    // Rute ini diakses dengan metode DELETE. Rute ini untuk menghapus data absen:
    $app->delete("/absensi/{id_absensi}", function (Request $request, Response $response, $args) {
        $id_absensi = $args["id_absensi"];
        $sql = "DELETE FROM absensi WHERE id_absensi=:id_absensi";
        $stmt = $this->db->prepare($sql);

        $data = [
            ":id_absensi" => $id_absensi
        ];

        $sql = "SELECT id_absensi FROM absensi WHERE id_absensi=:id_absensi";
        $stmtcek = $this->db->prepare($sql);
        $stmtcek->execute([":id_absensi" => $id_absensi]);

        //Jika ada
        if (!$stmtcek->rowCount() > 0) {

            return $response->withJson(["error" => true, "pesan" => "Data tidak ditemukan."], 200);
        } else {

            if ($stmt->execute($data)) {

                return $response->withJson(["error" => false, "pesan" => "Hapus data berhasil."], 200);
            }
        }


        return $response->withJson(["error" => true, "pesan" => "Data gagal dihapus"], 200);
    });
};
