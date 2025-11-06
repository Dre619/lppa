<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Notice Document</title>
  <style>
    .doc-frame {
      width: 100%;
      height: 600px;
      border: none;
    }
    .btn {
      padding: 10px 20px;
      background-color: #337ab7;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .btn:hover {
      background-color: #286090;
    }
  </style>
</head>
<body>
  <div style="margin: 20px;">
    <h2>Generated Notice Document</h2>

    <iframe class="doc-frame"
            src="https://docs.google.com/gview?url={{ urlencode(url('storage/' . $output)) }}&embedded=true"
            width="100%"
            height="600px">
    </iframe>

     <p>
        <a class="btn" href="{{ route('notice.download', basename($output)) }}">Download Document</a>
    </p>
</div>
</body>
</html>
