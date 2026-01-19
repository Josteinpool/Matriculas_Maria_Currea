/* ======== CAMBIO DE PESTAÑAS EN EL HOME ======== */
document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".tab");
  const sections = document.querySelectorAll(".content");

  tabs.forEach((tab, index) => {
    tab.addEventListener("click", () => {
      // Quitar clase activa de todas las pestañas
      tabs.forEach(t => t.classList.remove("active"));
      // Ocultar todas las secciones
      sections.forEach(s => (s.style.display = "none"));

      // Activar la pestaña seleccionada
      tab.classList.add("active");
      sections[index].style.display = "block";
    });
  });
});


// === Mostrar / ocultar campo de discapacidad ===
document.addEventListener("DOMContentLoaded", () => {
  const diagnostico = document.getElementById("discapacidad-diagnostico");
  const divDiscapacidad = document.getElementById("discapacidad-div");

  if (diagnostico && divDiscapacidad) {
    diagnostico.addEventListener("change", () => {
      const mostrar = diagnostico.value === "si";
      divDiscapacidad.style.display = mostrar ? "block" : "none";
      
      // Limpiar los campos si se oculta
      if (!mostrar) {
        document.getElementById("discapacidad").value = "";
        document.getElementById("certificado").value = "";
      }
    });
  }
});


// === Mostrar/ocultar campos de salud ===
document.addEventListener("DOMContentLoaded", () => {
  // Para alergias a medicamentos
  const alergiaSelect = document.getElementById("alergia-medicamentos");
  const alergiaDiv = document.getElementById("alergia-especificar-div");
  
  if (alergiaSelect && alergiaDiv) {
    alergiaSelect.addEventListener("change", () => {
      const mostrar = alergiaSelect.value === "si";
      alergiaDiv.style.display = mostrar ? "block" : "none";
      if (!mostrar) {
        document.getElementById("alergia-especificar").value = "";
      }
    });
  }

  // Para enfermedades diagnosticadas
  const enfermedadSelect = document.getElementById("enfermedad-diagnosticada");
  const enfermedadDiv = document.getElementById("enfermedad-especificar-div");
  
  if (enfermedadSelect && enfermedadDiv) {
    enfermedadSelect.addEventListener("change", () => {
      const mostrar = enfermedadSelect.value === "si";
      enfermedadDiv.style.display = mostrar ? "block" : "none";
      if (!mostrar) {
        document.getElementById("enfermedad-especificar").value = "";
      }
    });
  }
});
// === Validación condicional para discapacidad ===
document.addEventListener("DOMContentLoaded", () => {
    const formGeneral = document.getElementById("form-informacion-general");
    
    if (formGeneral) {
        formGeneral.addEventListener("submit", function(e) {
            const tieneDiscapacidad = document.getElementById("discapacidad-diagnostico").value;
            const tipoDiscapacidad = document.getElementById("discapacidad").value;
            const certificadoDiscapacidad = document.getElementById("certificado").value;
            
            let errores = [];
            
            // Validación condicional de discapacidad
            if (tieneDiscapacidad === "si") {
                if (!tipoDiscapacidad) {
                    errores.push("• Seleccione el tipo de discapacidad");
                }
                
                if (!certificadoDiscapacidad) {
                    errores.push("• Adjunte el certificado de discapacidad");
                }
            }
            
            // Si hay errores, mostrar alerta y prevenir envío
            if (errores.length > 0) {
                e.preventDefault();
                alert("❌ Por favor complete los siguientes campos:\n\n" + errores.join("\n"));
                return false;
            }
            
            return true;
        });
    }
});