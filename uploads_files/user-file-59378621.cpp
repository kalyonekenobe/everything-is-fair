#pragma GCC optimize ("O3")
#include <bits/stdc++.h>
#define ll long long
const int INF = 1e9;
const int MOD = 1e9 + 7;
const int N = 1e5 + 5;
using namespace std;
long long a[1005][9005];
void output(){
    for(int i = 1; i <= 20; i++, cout<<"\n"){
        for(int j = 1; j <= 25; j++){
            cout<<a[i][j]<<" ";
        }
    }
}
int sum(int n){
    int sm = 0;
    while(n > 0){
        sm = (sm % MOD + (n % 10) % MOD) % MOD;
        n /= 10;
    }
    return sm;
}
int fn(int n, int s){
    int rt = 0;
    for(int i = pow(10, n - 1); i < pow(10, n); i++){
        if(sum(i) == s) rt++;
    }
    return rt;
}
int main(){
    ios_base::sync_with_stdio(0);
    cin.tie(0); cout.tie(0);
    int n, s;
    cin>>n>>s;
    for(int i = 1; i <= 9; i++){
        a[1][i] = 1;
    }
    for(int i = 2; i <= 1000; i++){
        for(int j = 1; j <= i * 9; j++){
            if(j < 10) a[i][j] = (a[i - 1][j] % MOD + a[i][j - 1] % MOD) % MOD;
            else a[i][j] = (a[i][j - 1] % MOD + a[i - 1][j] % MOD - a[i - 1][j - 10] % MOD) % MOD;
        }
    }
    //output();
    //cout<<fn(n, s)<<"\n";
    if(a[n][s] < 0) a[n][s] += MOD;
    cout<<a[n][s]<<"\n";
    return 0;
}
