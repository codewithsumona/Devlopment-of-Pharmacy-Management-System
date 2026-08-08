</div><!-- End .main-wrapper -->
</div><!-- End .app-container -->

<!-- Global Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Universal Modal Overlay -->
<div class="modal-overlay" id="appModalOverlay">
    <div class="modal-card" id="appModalCard">
        <div class="modal-header">
            <h3 id="modalTitle">Modal Header</h3>
            <button class="nav-icon-btn" onclick="closeModal()"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="modal-body" id="modalBody">
            <p>Modal body content goes here.</p>
        </div>
        <div class="modal-footer" id="modalFooter">
            <button class="btn btn-secondary" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<!-- Global JavaScript File -->
<script src="<?php echo $path_prefix ?? ''; ?>js/script.js"></script>
</body>
</html>
