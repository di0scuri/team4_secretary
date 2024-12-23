import React from 'react';
import { TouchableOpacity, StyleSheet } from 'react-native';
import { NavigationContainer } from '@react-navigation/native';
import { createDrawerNavigator } from '@react-navigation/drawer';
import { createStackNavigator } from '@react-navigation/stack';
import { Ionicons } from '@expo/vector-icons';
import CustomDrawer from './components/CustomDrawer';
import SCalendar from './screens/SCalendar'; // Ensure the path is correct
import SApprovedProgram from './screens/SApprovedProgram'; // Ensure the path is correct
import SEvents from './screens/SEvents'; // Ensure the path is correct
import SResources from './screens/SResources'; // Ensure the path is correct
import SBeneficiary from './screens/SBeneficiary'; // Ensure the path is correct
import SFunds from './screens/SFunds';
import SMaterials from './screens/SMaterials';
import Agenda from './screens/Agenda';
import Attendance from './screens/Attendance';
import SOtherExpenses from './screens/SOtherExpenses';
import SConfirmMeeting from './screens/SConfirmMeeting';
import SeeDetails from './screens/SeeDetails';
import Applicants from './screens/Applicants';
import SHistory from './screens/SHistory';
import SeeMore from './screens/SeeMore'; // Ensure the path is correct
import SYearDetails from './screens/SYearDetails';

const Drawer = createDrawerNavigator();
const Stack = createStackNavigator();

const DrawerToggleButton = ({ navigation }) => {
  return (
    <TouchableOpacity onPress={() => navigation.toggleDrawer()} style={styles.menuButton}>
      <Ionicons name="menu" size={24} color="#fff" />
    </TouchableOpacity>
  );
};

function MainStack() {
  return (
    <Stack.Navigator
      initialRouteName="SCalendar"
      screenOptions={{
        headerTitleAlign: 'center',
        headerStyle: {
          backgroundColor: '#710808',
        },
        headerTintColor: '#fff',
        headerTitleStyle: {
          fontSize: 18,
          fontWeight: 'bold',
        },
      }}
    >
      <Stack.Screen
        name="SCalendar"
        component={SCalendar}
        options={({ navigation }) => ({
          title: 'Calendar',
          headerLeft: () => <DrawerToggleButton navigation={navigation} />,
        })}
      />
      <Stack.Screen
        name="Agenda"
        component={Agenda}
        options={{ title: 'Agenda' }}
      />
            <Stack.Screen
        name="Attendance"
        component={Attendance}
        options={{ title: 'Attendance' }}
      />
        <Stack.Screen
        name="SOtherExpenses"
        component={SOtherExpenses}
        options={{ title: 'Other Expenses' }}
      />
       <Stack.Screen
        name="SConfirmMeeting"
        component={SConfirmMeeting}
        options={{ title: 'Confirmation' }}
      />

      <Stack.Screen
        name="SApprovedProgram"
        component={SApprovedProgram}
        options={({ navigation }) => ({
          title: 'Approved Program',
          headerLeft: () => <DrawerToggleButton navigation={navigation} />,
        })}
      />
      <Stack.Screen
        name="SeeDetails"
        component={SeeDetails}
        options={{ title: 'Program Details' }}
      />
      <Stack.Screen
        name="SEvents"
        component={SEvents}
        options={({ navigation }) => ({
          title: 'Events',
          headerLeft: () => <DrawerToggleButton navigation={navigation} />,
        })}
      />
      <Stack.Screen
        name="SResources"
        component={SResources}
        options={({ navigation }) => ({
          title: 'Resources',
          headerLeft: () => <DrawerToggleButton navigation={navigation} />,
        })}
      />
        <Stack.Screen
        name="SFunds"
        component={SFunds}
        options={{ title: 'Funds' }}
      />
        <Stack.Screen
        name="SMaterials"
        component={SMaterials}
        options={{ title: 'Materials' }}
      />
      <Stack.Screen
        name="SBeneficiary"
        component={SBeneficiary}
        options={({ navigation }) => ({
          title: 'Beneficiary Management',
          headerLeft: () => <DrawerToggleButton navigation={navigation} />,
        })}
      />
      <Stack.Screen
        name="Applicants"
        component={Applicants}
        options={{ title: 'Applicants' }}
      />
        <Stack.Screen
        name="SHistory"
        component={SHistory}
        options={{ title: 'History' }}
      />

<Stack.Screen
  name="SeeMore"
  component={SeeMore}
  options={{ title: 'Beneficiary Details' }}
/>

<Stack.Screen
  name="SYearDetails"
  component={SYearDetails}
  options={{ title: 'Year Details' }}
/>


    </Stack.Navigator>
  );
}

function App() {
  return (
    <NavigationContainer>
      <Drawer.Navigator drawerContent={(props) => <CustomDrawer {...props} />} screenOptions={{ headerShown: false }}>
        <Drawer.Screen name="MainStack" component={MainStack} options={{ title: 'Main Stack' }} />
      </Drawer.Navigator>
    </NavigationContainer>
  );
}

const styles = StyleSheet.create({
  menuButton: {
    marginLeft: 10,
  },
});

export default App;
