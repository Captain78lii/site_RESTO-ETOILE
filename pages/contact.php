<?php 
include($_SERVER['DOCUMENT_ROOT'] . '/header.php'); 
?>

<div class="container">
    <h2 style="text-align:center; color:var(--brand-red); margin-bottom: 30px;">Nous Contacter</h2>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
        <div class="form-box" style="margin: 0; max-width: 100%;">
            <h3>📍 Coordonnées</h3>
            <p style="margin: 15px 0;"><strong>Adresse :</strong><br> 14 place du Quatorze Juillet<br> 78260 ACHERES</p>
            <p style="margin: 15px 0;"><strong>Téléphone :</strong><br> 09 53 11 80 63</p>
            <p style="margin: 15px 0;"><strong>Horaires :</strong><br> Lundi au Samedi : 11h - 22h<br> Dimanche : Fermé</p>
        </div>

        <div class="form-box" style="margin: 0; max-width: 100%; display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; background-color: #101f3c; color: white;">
            <h3 style="color:var(--accent-gold);">Vous nous cherchez ?</h3>
            <p>Nous sommes situés sur la place principale, facilement accessible avec des parkings à proximité.</p>
            <br>
            <div style="width: 100%; height: 300px; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                <iframe 
                    src="/images/maps.png" 
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</div>

</body>
</html>