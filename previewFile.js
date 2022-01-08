function previewFile(file) {
    // プレビュー画像を追加する要素
    var preview = document.getElementsByClassName('preview');
  
    // FileReaderオブジェクトを作成
    var reader = new FileReader();
  
    // URLとして読み込まれたときに実行する処理
    reader.onload = function (e) {
      var imageUrl = e.target.result; // URLはevent.target.resultで呼び出せる
      var img = document.createElement("img"); // img要素を作成
      img.src = imageUrl; // URLをimg要素にセット
      preview.appendChild(img); // #previewの中に追加
    }
  
    // いざファイルをURLとして読み込む
    reader.readAsDataURL(file);
  }