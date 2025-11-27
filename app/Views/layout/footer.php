<?php

use App\Libraries\Agoda;
use App\Libraries\Klook;
?>
    </main>

    <footer id="footer">
      <div class="container mt-2">
        <div class="row footer-search">
          <div class="col-sm-8 col-xs-12">
            <ins class="klk-aff-widget lazy-iframe" data-wid="<?= Klook::AFFILIATE_ID ?>" data-height="340px" data-adid="<?= Klook::SEARCH_ADID ?>" data-lang="" data-prod="search_vertical" data-currency="USD">
              <a href="//www.klook.com/?aid=<?= Klook::AFFILIATE_ID ?>&aff_adid=<?= Klook::SEARCH_ADID ?>" aria-label="klook.com"></a>
            </ins>
          </div>

          <div class="col-sm-4 col-xs-12">
            <div class="agoda-search-box" data-aid="<?= Agoda::AFFILIATE_ID ?>" data-wid="adgshp-1853849391">
              <div id="adgshp-1853849391" title="Agoda search widget"></div>
            </div>
          </div>
        </div>

        <div class="row footer-widgets mt-2">
          <!-- footer widget about-->
          <div class="col-sm-8 col-xs-12">
            <div class="footer-widget f-widget-about">
              <div class="col-sm-12">
                <div class="row">
                  <h2 class="title border-top py-1 border-success rounded-3"><?= trans('About') ?></h4>
                  <div class="title-line"></div>
                  <p><?= trans('Footer About') ?></p>
                </div>
              </div>
            </div>
          </div><!-- /.col-sm-4 -->

          <!-- footer widget follow us-->
          <div class="col-sm-4 col-xs-12">
            <div class="footer-widget f-widget-follow">
              <div class="col-sm-12">
                <div class="row">
                  <h2 class="title border-top py-1 border-danger rounded-3"><?= trans('Social Media') ?></h4>
                  <div class="title-line"></div>
                  <ul class="d-flex list-unstyled">
                    <!--if facebook url exists-->
                    <li>
                      <a rel="nofollow" class="facebook" href="https://facebook.com/travelpzt/" aria-label="Facebook" target="_blank">
                        <i class="fa-brands fa-square-facebook"></i>
                      </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <!--if youtube url exists-->
                    <li>
                      <a rel="nofollow" class="youtube" href="https://youtube.com/@TravelPZ" aria-label="Youtube" target="_blank">
                        <i class="fa-brands fa-square-youtube"></i>
                      </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <!--if dailymotion url exists-->
                    <li>
                      <a rel="nofollow" class="dailymotion" href="https://dailymotion.com/travel.pz" aria-label="Dailymotion" target="_blank">
                        <i class="fa-brands fa-dailymotion"></i>
                      </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <!--if instagram url exists-->
                    <li>
                      <a rel="nofollow" href="https://instagram.com/travel.pz" aria-label="Instagram" target="_blank">
                        <i class="fa-brands fa-square-instagram"></i>
                      </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <!--if threads url exists-->
                    <li>
                      <a rel="nofollow" href="https://threads.com/@travel.pz" aria-label="Threads" target="_blank">
                        <i class="fa-brands fa-square-threads"></i>
                      </a>
                    </li>
                    &nbsp;
                    &nbsp;
                    <!--if linkedin url exists-->
                    <li>
                      <a rel="nofollow" class="linkedin" href="https://linkedin.com/in/travelpz" aria-label="Linkedin" target="_blank">
                        <i class="fab fa-linkedin"></i>
                      </a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <!-- Copyright -->
          <div class="footer-bottom">
            <div class="row">
              <div class="col-md-12">
                <div class="footer-bottom-left">
                  <p><?= trans('Footer Copyright') ?></p>
                </div>

                <div class="footer-bottom-right">
                  <ul class="nav-footer">
                  </ul>
                </div>
              </div>
            </div>
            <!-- .row -->
          </div>
        </div>
    </footer>

    <script defer type="text/javascript" src="<?= base_url('assets/js/bootstrap/5.3.0/bootstrap.bundle.min.js') ?>"></script>
    <script async type="text/javascript" src="<?= base_url('assets/js/klook/widget.js') ?>"></script>
    <script async type="text/javascript" src="<?= base_url('assets/js/agoda/widget.js') ?>"></script>
    <?php foreach ($js_sources ?? [] as $src): ?>
      <script defer src="<?= $src ?>"></script>
    <?php endforeach; ?>
    </body>

    </html>