import { Expo } from 'expo-server-sdk';

// Instancia del cliente Expo
const expo = new Expo();

export async function sendPushNotification(token, { title, body, data = {}, attachments }) {
  if (!Expo.isExpoPushToken(token)) {
    console.error('❌ Token Expo inválido:', token);
    return;
  }

  const message = {
    to: token,
    title,
    body,
    sound: 'default',
    ...(attachments ? { attachments } : {}),
    data: {
      ...data,
      ...(attachments ? { attachments } : {}),
    },
  };

  try {
    const response = await expo.sendPushNotificationsAsync([message]);
    console.log('✅ Notificación enviada:', response);
    return response;
  } catch (error) {
    console.error('💥 Error al enviar notificación:', error);
  }
}

export function getOther(userId, participant_one, participant_two) {
  if (userId === String(participant_one._id)) {
    return participant_two;
  } else if (userId === String(participant_two._id)) {
    return participant_one;
  } else {
    return null; // o podrías lanzar una excepción si prefieres
  }
}

export function getOtherParticipants(userId, participants) {
  const others = participants.filter(participant => String(participant._id) !== userId);
  return others.length > 0 ? others : null;
}