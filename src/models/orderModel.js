/**
 * Modèle commandes — libellés statuts et normalisation API.
 */

export const ORDER_STATUS_LABELS = {
  en_attente: 'En attente',
  confirmee: 'Confirmée',
  expediee: 'Expédiée',
  livree: 'Livrée',
  annulee: 'Annulée',
};

export const PAYMENT_MODE_LABELS = {
  carte: 'Carte bancaire',
  paypal: 'PayPal',
  virement: 'Virement',
  stripe: 'Stripe (carte bancaire)',
};

export function getOrderStatusLabel(statut) {
  return ORDER_STATUS_LABELS[statut] || statut;
}

export function getPaymentModeLabel(mode) {
  return PAYMENT_MODE_LABELS[mode] || mode;
}

export function normalizeOrderLine(line) {
  return {
    refProduit: line.ref_produit,
    name: line.libelle_produit,
    image: line.url_image || '',
    quantity: line.quantite,
    unitPrice: parseFloat(line.prix_unitaire),
    subtotal: parseFloat(line.prix_unitaire) * line.quantite,
  };
}

export function normalizeOrder(order) {
  const lignes = (order.lignes || []).map(normalizeOrderLine);

  return {
    id: order.id_commande,
    date: order.date_commande,
    total: parseFloat(order.total),
    status: order.statut,
    statusLabel: getOrderStatusLabel(order.statut),
    lines: lignes,
    payment: order.paiement
      ? {
          mode: order.paiement.mode_paiement,
          modeLabel: getPaymentModeLabel(order.paiement.mode_paiement),
          date: order.paiement.date_paiement,
          amount: parseFloat(order.paiement.montant),
        }
      : null,
  };
}
