import { sendVerificationEmail } from '../utils/email';
import { generateToken } from '../utils/token';
import { User } from '../models/User';

export const signup = async (req, res) => {
  try {
    const { email, password, name } = req.body;

    // Check if user already exists
    const existingUser = await User.findOne({ email });
    if (existingUser) {
      return res.status(400).json({ message: 'Email already registered' });
    }

    // Create verification token
    const verificationToken = generateToken();

    // Create new user
    const user = new User({
      email,
      password,
      name,
      verified: false,
      verificationToken
    });

    await user.save();

    // Send verification email
    await sendVerificationEmail(email, verificationToken);

    res.status(201).json({ message: 'Please check your email to verify your account' });
  } catch (error) {
    res.status(500).json({ message: 'Error creating user' });
  }
}; 