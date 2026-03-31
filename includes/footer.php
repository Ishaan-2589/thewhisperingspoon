<!-- Footer -->
<footer id="contact">
  <p>© 2025 The Whispering Spoon. All rights reserved.</p>
  <p>123 Food Street, Delhi | 📞 +91-9876543210 | 📧 info@thewhisperingspoon.in</p>
</footer>

<!-- Logout Confirmation Modal -->
<div id="logoutModal" class="modal-overlay">
  <div class="modal-box">
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to log out?</p>
    <div class="modal-controls" style="display:flex; justify-content:space-around; gap:10px; margin-top:18px;">
      <button id="cancelLogout" type="button">Cancel</button>
      <button id="confirmLogout" type="button" class="confirm">Logout</button>
    </div>
  </div>
</div>

<script src="../assets/js/menu.js"></script>
<script>
  document.querySelectorAll('.logout-trigger').forEach(el => {
    el.addEventListener('click', (e) => {
      e.preventDefault();
      document.getElementById('logoutModal').style.display = 'flex';
    });
  });

  document.getElementById('cancelLogout').addEventListener('click', () => {
    document.getElementById('logoutModal').style.display = 'none';
  });

  document.getElementById('confirmLogout').addEventListener('click', () => {
    window.location.href = '/TheWhisperingSpoon/auth/logout.php';
  });
</script>
</body>
</html>
