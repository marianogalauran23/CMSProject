document.addEventListener('DOMContentLoaded', () => {
    const loginBtn = document.querySelector(".loginbtn");
    const signupBtn = document.querySelector(".signupbutton");
    const heroImage = document.querySelector(".hero-image");
    const floatingImage = document.querySelector('.floating-image');
    const rocketImage  = document.querySelector('.floating-image-rocket');
    const qnaSection = document.querySelector('.qna');
    const reviewParagraphs = document.querySelectorAll('.reviews_container p');
    const programmingImg = document.querySelector('.programming_img');
    const heroSection = document.querySelector('.hero #hero-text');
  
  
    function checkVisibility() {
      reviewParagraphs.forEach(paragraph => {
        const rect = paragraph.getBoundingClientRect();
        const isVisible = (
          rect.top >= 0 &&
          rect.left >= 0 &&
          rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
          rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
  
        if (isVisible && !paragraph.classList.contains('visible')) {
          paragraph.classList.add('visible');
        }
      });
  
      if (programmingImg) {
        const rect = programmingImg.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        let visiblePercentage = 0;
  
        if (rect.top < windowHeight && rect.bottom > 0) {
          const visibleHeight = Math.min(rect.bottom, windowHeight) - Math.max(rect.top, 0);
          const imageHeight = rect.bottom - rect.top;
          visiblePercentage = visibleHeight / imageHeight;
        }
        programmingImg.style.opacity = visiblePercentage;
      }
  
      if (heroSection) {
        const rect = heroSection.getBoundingClientRect();
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        let visiblePercentage = 0;
  
        if (rect.top < windowHeight && rect.bottom > 0) {
          const visibleHeight = Math.min(rect.bottom, windowHeight) - Math.max(rect.top, 0);
          const sectionHeight = rect.bottom - rect.top;
          visiblePercentage = visibleHeight / sectionHeight;
        }
        heroSection.style.opacity = visiblePercentage;
      }
    }
  
    function decreaseBlur(){
      heroImage.style.filter = "brightness(0.7) contrast(0.9) blur(2px)";
    };
  
    function increaseBlur(){
      heroImage.style.filter = "brightness(0.4) contrast(0.9) blur(7px)";
    };
  
    window.addEventListener('scroll', () => {
      const scrollPosition = window.scrollY;
      floatingImage.style.transform = `translateY(${scrollPosition * -0.85}px)`;
      rocketImage .style.transform = `translateY(${scrollPosition * -0.85}px)`;
    });
  
    function checkQnaVisibility() {
      if (qnaSection) {
        const rect = qnaSection.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
  
        if (isVisible && !qnaSection.classList.contains('in-view')) {
          qnaSection.classList.add('in-view');
        } else if (!isVisible && qnaSection.classList.contains('in-view')) {
          qnaSection.classList.remove('in-view');
        }
      }
    }

    function goToDahboard(){
      const usernameInput = document.querySelector('.username');
      const usernameValue = usernameInput.value.trim();
      if (usernameValue) {
        window.location.href = "./pages/dashboard.php?username=" + encodeURIComponent(usernameValue);
      } else {
        alert("Please enter a username.");
      }
    }

    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            document.querySelectorAll('input').forEach(input => input.value = '');
        }
    });

    window.addEventListener('load', checkQnaVisibility);
    window.addEventListener('scroll', checkQnaVisibility);
    loginBtn.addEventListener("mouseover", decreaseBlur);
    signupBtn.addEventListener("mouseover", decreaseBlur);
    loginBtn.addEventListener("mouseout", increaseBlur);
    signupBtn.addEventListener("mouseout", increaseBlur);
    window.addEventListener('load', checkVisibility);
    window.addEventListener('scroll', checkVisibility);
  });