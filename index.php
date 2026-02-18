<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>QRIS Payment</title>
<style>
body {
  background:#eaeaea;
  font-family: Arial, sans-serif;
}
.qris-card {
  width: 360px;
  margin: 30px auto;
  background: white;
  border-radius: 10px;
  overflow: hidden;
}
.header {
  display:flex;
  justify-content:space-between;
  padding:15px;
}
.header img {
  height:55px;
}
.content {
  text-align:center;
  padding:10px 20px 20px;
}
.merchant {
  font-weight:bold;
  font-size:18px;
}
.nmid {
  font-size:12px;
  color:#555;
  margin-top:4px;
}
.qr {
  margin:15px 0;
}
.qr img {
  width:260px;
}
.nominal {
  font-size:20px;
  font-weight:bold;
}
.footer {
  font-size:11px;
  color:#666;
  text-align:center;
  padding-bottom:15px;
}
.form {
  padding:15px;
}
input,button {
  width:100%;
  padding:10px;
  margin-top:8px;
}
button {
  background:#d32f2f;
  color:white;
  border:none;
  border-radius:6px;
}
</style>
</head>
<body>

<div class="qris-card">
  <div class="header">
    <img src="qris-logo.png">
    <img src="gpn-logo.png">
  </div>

<?php if (!empty($_GET['nominal'])):
  $baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") .
           "://".$_SERVER['HTTP_HOST'] .
           dirname($_SERVER['PHP_SELF']);

$metaUrl = $baseUrl . "/qris.php?meta=1&nominal=" . intval($_GET['nominal']);

$meta = json_decode(file_get_contents($metaUrl), true);
?>

  <div class="content">
    <div class="merchant"><?= strtoupper($meta['merchant']) ?></div>
    <div class="nmid">NMID : <?= $meta['nmid'] ?></div>

    <div class="qr">
      <img src="qris.php?nominal=<?= intval($_GET['nominal']) ?>">
    </div>

    <div class="nominal">
      Rp <?= number_format($_GET['nominal'],0,',','.') ?>
    </div>
  </div>

  <div class="footer">
    SATU QRIS UNTUK SEMUA<br>
    www.qris.id
  </div>

<?php endif; ?>

  <div class="form">
    <form>
     <input type="text" id="nominal_view" placeholder="Masukkan nominal" autocomplete="off" required>
<input type="hidden" name="nominal" id="nominal_real">
      <button>Buat QRIS</button>
    </form>
  </div>
</div>

<script>
const view = document.getElementById('nominal_view');
const real = document.getElementById('nominal_real');

view.addEventListener('input', function () {
  let angka = this.value.replace(/\D/g, '');
  if (!angka) {
    real.value = '';
    this.value = '';
    return;
  }
  real.value = angka;
  this.value = angka.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
});
</script>

</body>
</html>