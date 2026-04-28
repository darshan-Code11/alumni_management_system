<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = "About Us - AlumniConnect";
?>
<?php include 'includes/header.php'; ?>

<!-- Hero -->
<div class="page-header position-relative overflow-hidden">
  <div class="blob-shape" style="width:400px;height:400px;top:-100px;left:-100px;background:#10b981;animation-delay:0.5s;"></div>
  <div class="blob-shape bg-warning" style="width:250px;height:250px;bottom:-80px;right:-40px;animation-delay:1.5s;"></div>
  <div class="container position-relative z-1 text-center py-4">
    <span class="badge mb-3 px-4 py-2 rounded-pill" style="background:rgba(255,255,255,0.15);color:#fff;font-size:1rem;">
      <i class="fas fa-graduation-cap me-2"></i>AlumniConnect
    </span>
    <h1 class="fw-bold display-5">About <span class="text-gradient text-white">AlumniConnect</span></h1>
    <p class="fs-5 opacity-75 mx-auto" style="max-width:640px;">
      A modern platform built to keep graduates connected, informed, and empowered throughout their professional journey.
    </p>
  </div>
</div>

<!-- Mission -->
<section class="container py-5">
  <div class="row align-items-center g-5">
    <div class="col-md-6">
      <h2 class="fw-bold mb-3"><i class="fas fa-bullseye me-2 text-primary"></i>Our Mission</h2>
      <p class="text-muted fs-5 mb-4">
        AlumniConnect was built with a single mission — to bridge the gap between graduates and their alma mater.
        We believe that the connections made during college should last a lifetime.
      </p>
      <ul class="list-unstyled">
        <?php
        $bullets = [
          ['fas fa-check-circle', 'text-success', 'Digitize alumni record management'],
          ['fas fa-check-circle', 'text-success', 'Improve engagement between alumni and institutions'],
          ['fas fa-check-circle', 'text-success', 'Provide a secure, role-based access system'],
          ['fas fa-check-circle', 'text-success', 'Enable job sharing among the network'],
          ['fas fa-check-circle', 'text-success', 'Organize events and reunions easily'],
        ];
        foreach ($bullets as $b): ?>
        <li class="mb-2 d-flex align-items-center">
          <i class="<?= $b[0] ?> <?= $b[1] ?> me-3 fs-5"></i>
          <span><?= $b[2] ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="col-md-6">
      <div class="card border-0 shadow-sm p-5 text-center" style="border-radius:24px;background:linear-gradient(135deg,#7c3aed15,#10b98115);">
        <i class="fas fa-users" style="font-size:5rem;color:#7c3aed;"></i>
        <h3 class="fw-bold mt-4 mb-2">Connecting Graduates</h3>
        <p class="text-muted">Building a community that lasts beyond graduation day.</p>
      </div>
    </div>
  </div>
</section>

<!-- Features -->
<section class="py-5" style="background:#f8f7ff;">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="fw-bold">What We <span class="text-gradient" style="-webkit-background-clip:text;-webkit-text-fill-color:transparent;background:linear-gradient(90deg,#7c3aed,#10b981);">Offer</span></h2>
      <p class="text-muted">Everything you need to stay connected with your alumni network.</p>
    </div>
    <div class="row g-4">
      <?php
      $features = [
        ['fas fa-user-circle',  '#7c3aed', 'Alumni Profiles',    'Create and manage your professional profile visible to your network.'],
        ['fas fa-users',        '#10b981', 'Alumni Directory',   'Browse and discover fellow graduates from your institution.'],
        ['fas fa-comments',     '#f59e0b', 'Real-time Chat',     'Stay in touch with alumni through our built-in messaging system.'],
        ['fas fa-briefcase',    '#7c3aed', 'Job Portal',         'Share and discover job opportunities posted by fellow alumni.'],
        ['fas fa-calendar',     '#10b981', 'Events & Reunions',  'Stay updated on upcoming alumni events, meetups and reunions.'],
        ['fas fa-shield-alt',   '#f59e0b', 'Secure Access',      'Role-based authentication ensures your data is always safe.'],
      ];
      foreach ($features as $f): ?>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 p-4 hover-glow" style="border-radius:16px;border-top:4px solid <?= $f[1] ?> !important;">
          <i class="<?= $f[0] ?> mb-3" style="font-size:2.2rem;color:<?= $f[1] ?>;"></i>
          <h6 class="fw-bold mb-2"><?= $f[2] ?></h6>
          <p class="text-muted small mb-0"><?= $f[3] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Tech Stack -->
<section class="container py-5">
  <div class="text-center mb-5">
    <h2 class="fw-bold">Technology <span style="color:#7c3aed;">Stack</span></h2>
    <p class="text-muted">Built with modern web technologies for performance and reliability.</p>
  </div>
  <div class="row g-3 justify-content-center">
    <?php
    $tech = [
      ['fab fa-html5',    '#e34f26', 'HTML5'],
      ['fab fa-css3-alt', '#264de4', 'CSS3'],
      ['fab fa-js',       '#f7df1e', 'JavaScript'],
      ['fab fa-php',      '#777bb4', 'PHP'],
      ['fas fa-database', '#00758f', 'MySQL'],
      ['fab fa-bootstrap','#7952b3', 'Bootstrap'],
    ];
    foreach ($tech as $t): ?>
    <div class="col-6 col-md-2">
      <div class="card border-0 shadow-sm text-center p-3 hover-glow" style="border-radius:16px;">
        <i class="<?= $t[0] ?> mb-2" style="font-size:2.5rem;color:<?= $t[1] ?>;"></i>
        <div class="fw-semibold small"><?= $t[2] ?></div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- Developer -->
<section class="py-5" style="background:linear-gradient(135deg,#7c3aed,#10b981);">
  <div class="container text-center text-white">
    <i class="fas fa-code mb-3" style="font-size:3rem;"></i>
    <h3 class="fw-bold mb-2">Developed By</h3>
    <h4 class="mb-1">Darshan Gowda</h4>
    <p class="opacity-75 mb-4">Full Stack Web Developer | Final Year Project</p>
    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="https://github.com/darshan-Code11/alumni_management_system" target="_blank" class="btn btn-light px-4">
        <i class="fab fa-github me-2"></i>GitHub Repository
      </a>
      <a href="http://alumnimanaegement.42web.io/" target="_blank" class="btn btn-outline-light px-4">
        <i class="fas fa-globe me-2"></i>Live Demo
      </a>
    </div>
  </div>
</section>

<!-- Contact -->
<section class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card border-0 shadow-sm p-5 text-center" style="border-radius:24px;">
        <h3 class="fw-bold mb-3"><i class="fas fa-envelope me-2 text-primary"></i>Get In Touch</h3>
        <p class="text-muted mb-4">Have questions about the project or want to collaborate? Reach out!</p>
        <a href="mailto:darshan@example.com" class="btn btn-primary px-5">
          <i class="fas fa-paper-plane me-2"></i>Send Email
        </a>
      </div>
    </div>
  </div>
</section>

<div class="py-2"></div>
<?php include 'includes/footer.php'; ?>
