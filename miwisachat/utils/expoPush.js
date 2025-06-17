import { Expo } from 'expo-server-sdk';

// Instancia del cliente Expo
const expo = new Expo();

export async function sendPushNotification(token, { title, body, data = {} }) {
  if (!Expo.isExpoPushToken(token)) {
    console.error('❌ Token Expo inválido:', token);
    return;
  }

  const message = {
    to: token,
    sound: 'default',
    title,
    body,
    data,
  };

  try {
    const response = await expo.sendPushNotificationsAsync([message]);
    console.log('✅ Notificación enviada:', response);
    return response;
  } catch (error) {
    console.error('💥 Error al enviar notificación:', error);
  }
}

export function getOther(user_id, participant_one, participant_two) {
  if (user_id === participant_one) {
    return participant_two;
  } else if (user_id === participant_two) {
    return participant_one;
  } else {
    return null; // o podrías lanzar una excepción si prefieres
  }
}
