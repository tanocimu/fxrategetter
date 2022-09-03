//+------------------------------------------------------------------+
//|                                                   RATESENDER.mq4 |
//|                                                               wy |
//|                          https://loserchecker.000webhostapp.com/ |
//+------------------------------------------------------------------+
#property copyright "wy"
#property link      "https://loserchecker.000webhostapp.com/"
#property version   "1.00"
#property strict

int count = 0;

int OnInit()
{
   EventSetMillisecondTimer(2000);//タイマーを2秒間隔に設定
   return(INIT_SUCCEEDED);
}

void OnDeinit(const int reason)
{
   EventKillTimer();
}

void OnTick()
{

}

string SendPost(string URL, string str){
   str = "value1=1" + "&value2=" + str;
   int WebR; 
   int timeout = 30000;
   string cookie = NULL,headers; 
   char post[],ReceivedData[]; 
   
   StringToCharArray( str, post );
   WebR = WebRequest( "POST", URL, cookie, NULL, timeout, post, 0, ReceivedData, headers );
   if(!WebR) Print("Web request failed");

   //CharArrayToStringにて、charの値0(ASCIIコードでNULL)の場合、そこで文字列変換が止まるため、スペース（32）に変更。
   for (int i = 0; i < ArraySize(ReceivedData); i++) {
      if(ReceivedData[i] == 0){
         ReceivedData[i] = 32;
      }
   }
   return CharArrayToString(ReceivedData); 
}

void OnTimer()
{
   count++;

   string symbolInfoString = DoubleToString(MarketInfo(Symbol(),MODE_ASK));
   //string symbolInfoString = SymbolInfoString("usdjpy",SYMBOL_CURRENCY_BASE);
   //Comment(SendPost("https://loserchecker.000webhostapp.com/",IntegerToString(count)));
   Comment(SendPost("http://localhost/fxrategetter/", symbolInfoString));
   Print("動作"+symbolInfoString);
}