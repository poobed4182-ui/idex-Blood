/**
 * Interactive Symptom Assessment, Dosage Calculator & Inspection Checklist
 */

document.addEventListener("DOMContentLoaded", () => {
  initSymptomChecker();
  initDosageCalculator();
  initChecklist();
  initNetlifyForm();
});

function initSymptomChecker() {
  const checkboxes = document.querySelectorAll('.symptom-item input[type="checkbox"]');
  const resultCard = document.getElementById("assessmentResult");
  const resultTitle = document.getElementById("resultTitle");
  const resultAdvice = document.getElementById("resultAdvice");
  if (!checkboxes.length || !resultCard) return;

  function calculateRisk() {
    let generalCount = 0;
    let dangerCount = 0;

    checkboxes.forEach((cb) => {
      const parent = cb.closest(".symptom-item");
      if (cb.checked) {
        parent.classList.add("active");
        if (cb.dataset.danger === "true") {
          dangerCount++;
        } else {
          generalCount++;
        }
      } else {
        parent.classList.remove("active");
      }
    });

    resultCard.className = "assessment-result";

    if (dangerCount > 0) {
      resultCard.classList.add("result-critical");
      resultTitle.innerHTML = "🚨 สัญญาณวิกฤต/ภาวะช็อก! (Critical Warning)";
      resultAdvice.innerHTML = `
        <strong>พบสัญญาณอันตรายขั้นวิกฤต ${dangerCount} อาการ!</strong> เสี่ยงต่อภาวะพลาสมาในเลือดรั่วไหล ความดันโลหิตตก หรือภาวะเลือดออกในอวัยวะภายในอย่างรุนแรง 
        <br>👉 <strong>คำแนะนำเร่งด่วน:</strong> รีบนำตัวส่งห้องฉุกเฉินของโรงพยาบาลที่ใกล้ที่สุดทันที หรือโทรสายด่วน <strong>1669</strong> ตลอด 24 ชั่วโมง <em>ห้ามรอสังเกตอาการที่บ้าน</em>
      `;
    } else if (generalCount >= 3) {
      resultCard.classList.add("result-high");
      resultTitle.innerHTML = "⚠️ มีความเสี่ยงสูงต่อโรคไข้เลือดออก (High Suspicion)";
      resultAdvice.innerHTML = `
        <strong>มีอาการเข้าข่ายกลุ่มโรคไข้เลือดออกเดงกี ${generalCount} ข้อ</strong> 
        <br>👉 <strong>คำแนะนำ:</strong> ควรไปพบแพทย์เพื่อตรวจร่างกายและตรวจความสมบูรณ์ของเม็ดเลือด (CBC) หรือชุดตรวจเร็ว NS1 Antigen 
        <br>⚠️ <em>ข้อห้ามสำคัญ:</em> ดื่มน้ำเกลือแร่หรือน้ำผลไม้ทดแทนน้ำในร่างกาย <strong>ห้ามรับประทานยากลุ่มไอบูโพรเฟน แอสไพริน หรือยาชุดเด็ดขาด!</strong> ทานได้เฉพาะพาราเซตามอล
      `;
    } else if (generalCount >= 1) {
      resultCard.classList.add("result-medium");
      resultTitle.innerHTML = "🟡 ควรเฝ้าระวังอาการอย่างใกล้ชิด (Moderate Watch)";
      resultAdvice.innerHTML = `
        <strong>พบอาการเริ่มต้น ${generalCount} ข้อ</strong> อาการอาจเป็นได้ทั้งไข้หวัดทั่วไปหรือระยะแรกเริ่มของไข้เลือดออก 
        <br>👉 <strong>คำแนะนำ:</strong> สังเกตอาการในช่วง 48–72 ชั่วโมง หากไข้สูงลอยเกิน 38.5°C ไม่ลดลง หรือมีอาการคลื่นไส้ ปวดเบ้าตา ให้รีบไปพบแพทย์
      `;
    } else {
      resultCard.classList.add("result-low");
      resultTitle.innerHTML = "🟢 ยังไม่พบอาการผิดปกติที่ชัดเจน (Normal / Low Risk)";
      resultAdvice.innerHTML = `
        ยังไม่มีการติ๊กเลือกอาการใด หากท่านหรือคนใกล้ชิดมีไข้ สามารถเช็ดตัวลดไข้ ดื่มน้ำสะอาดพักผ่อน และสามารถกลับมาทำแบบประเมินนี้ได้ทุกเมื่อที่เริ่มมีอาการผิดปกติ
      `;
    }
  }

  checkboxes.forEach((cb) => {
    cb.addEventListener("change", calculateRisk);
  });
}

function initDosageCalculator() {
  const weightInput = document.getElementById("patientWeight");
  const resultElement = document.getElementById("dosageOutput");
  if (!weightInput || !resultElement) return;

  function calculateDose() {
    const weight = parseFloat(weightInput.value);
    if (!weight || weight <= 0) {
      resultElement.innerHTML = "กรุณาระบุน้ำหนักตัวเป็นตัวเลข (กิโลกรัม)";
      return;
    }

    if (weight > 200) {
      resultElement.innerHTML = "น้ำหนักเกินเกณฑ์มาตรฐาน กรุณาปรึกษาแพทย์";
      return;
    }

    // Standard clinical dosing: 10-15 mg/kg per dose, max 1000 mg (1g) per single dose for adults
    const minDose = Math.round(weight * 10);
    const maxDose = Math.min(Math.round(weight * 15), 1000);

    // Standard 500mg tablets estimation
    let tabletInfo = "";
    if (weight >= 50) {
      tabletInfo = "(= 1 เม็ด หรือไม่เกิน 1 เม็ดครึ่ง ชนิด 500 มก.)";
    } else if (weight >= 34) {
      tabletInfo = "(= ประมาณ 1 เม็ด ชนิด 500 มก.)";
    } else if (weight >= 17) {
      tabletInfo = "(= ประมาณ 1/2 เม็ด ชนิด 500 มก. หรือยาน้ำสำหรับเด็ก)";
    } else {
      tabletInfo = "(แนะนำใช้ยาน้ำเชื่อมลดไข้สำหรับเด็กคำนวณตามซีซี)";
    }

    resultElement.innerHTML = `
      ขนาดยาที่เหมาะสม: <strong>${minDose} – ${maxDose} มิลลิกรัม ต่อครั้ง</strong> ${tabletInfo}
      <br><span style="font-size: 0.9rem; font-weight: 500; color: #4A5568;">
        🕒 ทานห่างกันทุก <strong>4 – 6 ชั่วโมง</strong> เมื่อมีไข้ (ห้ามทานเกินวันละ 4,000 มก. หรือไม่เกิน 4-5 ครั้งต่อวัน เพื่อป้องกันตับทำงานหนัก)
      </span>
    `;
  }

  weightInput.addEventListener("input", calculateDose);
}

function initChecklist() {
  const items = document.querySelectorAll(".inspect-checkbox");
  const progressText = document.getElementById("inspectProgress");
  const progressBar = document.getElementById("inspectProgressBar");
  if (!items.length || !progressText) return;

  function updateProgress() {
    const total = items.length;
    const checked = Array.from(items).filter(i => i.checked).length;
    const percent = Math.round((checked / total) * 100);

    progressText.textContent = `สำรวจและกำจัดแล้ว ${checked}/${total} จุด (${percent}%)`;
    if (progressBar) {
      progressBar.style.width = `${percent}%`;
    }
  }

  items.forEach(item => {
    item.addEventListener("change", updateProgress);
  });
}

function initNetlifyForm() {
  const form = document.getElementById("communityReportForm");
  const alertBox = document.getElementById("formAlert");
  if (!form || !alertBox) return;

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    try {
      const formData = new FormData(form);
      const body = new URLSearchParams(formData).toString();

      const response = await fetch("/", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body,
      });

      if (response.ok) {
        alertBox.className = "form-alert success";
        alertBox.textContent = "✅ บันทึกข้อมูลรายงานจุดเสี่ยงเรียบร้อยแล้ว ขอบคุณที่ร่วมปกป้องชุมชนจากไข้เลือดออก!";
        form.reset();
      } else {
        throw new Error("เกิดข้อผิดพลาดในการส่งข้อมูล");
      }
    } catch (err) {
      alertBox.className = "form-alert error";
      alertBox.textContent = "⚠️ ไม่สามารถส่งข้อมูลได้ในขณะนี้ กรุณาลองใหม่อีกครั้ง";
    } finally {
      if (submitBtn) submitBtn.disabled = false;
    }
  });
}
