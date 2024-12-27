--Requête pour afficher le nombre de bienfaisants regroupé par nom de l’opération

SELECT nomOp,  COUNT(d.idBien) AS nombreBienfaisants FROM Operation 
LEFT JOIN 
 Donation d ON o.idOp = d.idOp
GROUP BY Nom ;
    



--Procédure pour afficher les donations d'une opération donnée pour l'année en cours
DELIMITER //
CREATE PROCEDURE GetDonationsByOperation(IN operationId INT)
BEGIN
    SELECT 
        d.montantDonation, 
        b.nomBien, 
        b.prenomBien
    FROM 
        Donation d
    JOIN 
        Bienfaisant b ON d.idBien = b.idBien
    WHERE 
        d.idOp = operationId 
        AND YEAR(d.dateDonation) = YEAR(CURDATE());
END //

 


--Trigger pour mettre à jour le champ cumulMontant
DELIMITER //
CREATE TRIGGER UpdateCumulMontant
AFTER INSERT ON Donation
FOR EACH ROW
BEGIN  UPDATE Operation
    SET cumulMontant = cumulMontant + NEW.montantDonation
    WHERE idOp = NEW.idOp;
END //






--Procédure pour ajouter une ligne à la table Donation
DELIMITER //
CREATE PROCEDURE AddDonation(
    IN donationAmount DECIMAL(10,2),
    IN operationId INT,
    IN benefactorId INT
)
BEGIN
    INSERT INTO Donation (dateDonation, montantDonation, idOp, idBien)
    VALUES (CURDATE(), donationAmount, operationId, benefactorId);
END //




-- . Fonction pour retourner le montant total des donations pour une famille
DELIMITER //
CREATE FUNCTION GetTotalDonationsByFamily(familyId INT)
RETURNS DECIMAL(10,2)
DETERMINISTIC
BEGIN
    DECLARE totalDonation DECIMAL(10,2);

    SELECT SUM(d.montantDonation)
    INTO totalDonation
    FROM Donation d
    JOIN Operation o ON d.idOp = o.idOp
    WHERE o.idFamille = familyId;

    RETURN IFNULL(totalDonation, 0);
END //


