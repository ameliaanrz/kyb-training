<?php
require_once __DIR__ . '/../includes/pagination.inc.php';
?>

<nav class="mt-5">
  <ul class="pagination justify-content-end">
    <li class="page-item <?php echo $currPage == 1 ? 'disabled' : ''; ?>">
      <a class="page-link" href="?page=<?php echo $currPage - 1; ?><?php echo empty($params) ? "" : "&" . $params; ?>" tabindex="-1">Back</a>
    </li>
    <?php for ($i = $startPage; $i <= $endPage; $i++) : ?>
      <li class="page-item <?php echo $i == $currPage ? 'active' : ''; ?>"><a class="page-link" href="?page=<?php echo $i; ?><?php echo empty($params) ? "" : "&" . $params; ?>"><?php echo $i; ?></a></li>
    <?php endfor; ?>
    <li class="page-item <?php echo $currPage >= $pageCount ? 'disabled' : ''; ?>">
      <a class="page-link" href="?page=<?php echo $currPage + 1; ?><?php echo empty($params) ? "" : "&" . $params; ?>" tabindex="-1">Next</a>
    </li>
  </ul>
</nav>