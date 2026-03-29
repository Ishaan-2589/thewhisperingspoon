
let vegOnly = false;

// CATEGORY + VEG FILTER
function filterMenu(category, btn) {
  const headings = document.querySelectorAll('.category-heading');
  const cards = document.querySelectorAll('.menu-card');
  const filterButtons = document.querySelectorAll('.filter-btn');

  if (btn) {
    filterButtons.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }

  headings.forEach(heading => {
    heading.style.display =
      category === 'all' ||
      heading.textContent.toLowerCase().includes(category)
        ? 'block'
        : 'none';
  });

  cards.forEach(card => {
    const categoryMatch =
      category === 'all' || card.classList.contains(category);

    const vegMatch =
      !vegOnly || card.classList.contains('veg');

    card.style.display =
      categoryMatch && vegMatch ? 'block' : 'none';
  });
}

// VEG TOGGLE
function applyVegFilter() {
  vegOnly = document.getElementById("vegToggle").checked;

  const activeBtn = document.querySelector(".filter-btn.active");

  if (activeBtn) {
    filterMenu(activeBtn.textContent.toLowerCase(), activeBtn);
  } else {
    filterMenu('all', null);
  }
}

// LIGHTBOX
function openLightbox(imgElement) {
  const lightbox = document.getElementById("lightbox");
  const lightboxImg = document.getElementById("lightbox-img");
  lightbox.style.display = "flex";
  lightboxImg.src = imgElement.src;
}

function closeLightbox() {
  document.getElementById("lightbox").style.display = "none";
}

// SCROLL TO TOP
const scrollTopBtn = document.getElementById("scrollTopBtn");

window.onscroll = () => {
  scrollTopBtn.style.display =
    document.body.scrollTop > 100 || document.documentElement.scrollTop > 100
      ? "block"
      : "none";
};

function topFunction() {
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

document.querySelectorAll(".add-cart-btn").forEach(btn => {
  btn.addEventListener("click", function () {

    const id = this.dataset.id;

    fetch("/TheWhisperingSpoon/user/add-to-cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded"
      },
      body: "id=" + id
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {

        // Update cart count in navbar
        const cartLink = document.querySelector("nav a[href*='cart.php']");
        if (cartLink) {
          cartLink.innerHTML = "🛒 Cart (" + data.totalItems + ")";
        }

        showToast("Item added to cart ✅");
      }
    });

  });
});


function showToast(message) {
  const toast = document.createElement("div");
  toast.className = "toast-message";
  toast.innerText = message;

  document.body.appendChild(toast);

  setTimeout(() => {
    toast.classList.add("show");
  }, 100);

  setTimeout(() => {
    toast.remove();
  }, 3000);
}
