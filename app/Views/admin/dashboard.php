<!DOCTYPE html>
<html>

<head>
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    .dashboard-card {
      cursor: pointer;
      color: white;
      height: 150px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      transition: transform 0.3s, box-shadow 0.3s;
    }

    .dashboard-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .card-users {
      background-color: #007bff;
    }

    .card-sources {
      background-color: #28a745;
    }

    .card-languages {
      background-color: #17a2b8;
    }

    .card-locations {
      background-color: #ffc107;
    }

    .card-categories {
      background-color: #dc3545;
    }

    .card-items {
      background-color: #6f42c1;
    }
  </style>
</head>

<body class="bg-light">

  <div class="container mt-5">
    <h2 class="text-center mb-5">Admin Dashboard</h2>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="card dashboard-card card-users text-center" onclick="location.href='<?= base_url('admin/users') ?>'">
          <div class="card-body">
            <h5 class="card-title">Users</h5>
            <p class="card-text">Manage users, roles, and permissions.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card dashboard-card card-sources text-center" onclick="location.href='<?= base_url('admin/sources') ?>'">
          <div class="card-body">
            <h5 class="card-title">Sources</h5>
            <p class="card-text">Manage sources of data.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card dashboard-card card-languages text-center" onclick="location.href='<?= base_url('admin/languages') ?>'">
          <div class="card-body">
            <h5 class="card-title">Languages</h5>
            <p class="card-text">Manage languages for translations.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card dashboard-card card-locations text-center" onclick="location.href='<?= base_url('admin/locations') ?>'">
          <div class="card-body">
            <h5 class="card-title">Locations</h5>
            <p class="card-text">Manage locations and translations.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card dashboard-card card-categories text-center" onclick="location.href='<?= base_url('admin/categories') ?>'">
          <div class="card-body">
            <h5 class="card-title">Categories</h5>
            <p class="card-text">Manage categories and translations.</p>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="card dashboard-card card-items text-center" onclick="location.href='<?= base_url('admin/items') ?>'">
          <div class="card-body">
            <h5 class="card-title">Items</h5>
            <p class="card-text">Manage hotels, activities, and items.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

</body>

</html>